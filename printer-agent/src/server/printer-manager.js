const { exec, spawn } = require('child_process');
const { promisify } = require('util');
const fs = require('fs');
const path = require('path');
const os = require('os');
const Config = require('../utils/config');
const Logger = require('../utils/logger');

const execAsync = promisify(exec);

let defaultPrinter = null;

async function initializePrinterManager() {
  try {
    const config = Config.getConfig();
    if (config.printerName) {
      defaultPrinter = config.printerName;
      Logger.info('Impressora padrão configurada:', { printer: defaultPrinter });
    } else {
      // Tentar usar impressora padrão do Windows
      const printers = await listPrinters();
      if (printers.length > 0) {
        // Encontrar impressora padrão
        const defaultPrinterObj = printers.find(p => p.isDefault);
        defaultPrinter = defaultPrinterObj ? defaultPrinterObj.name : printers[0].name;
        Logger.info('Usando impressora:', { printer: defaultPrinter });
      }
    }
  } catch (error) {
    Logger.error('Erro ao inicializar gerenciador de impressoras', { error: error.message });
  }
}

async function listPrinters() {
  try {
    // Usar PowerShell para listar impressoras do Windows
    const command = 'powershell -Command "Get-Printer | Select-Object Name, PrinterStatus | ConvertTo-Json"';
    const { stdout, stderr } = await execAsync(command);
    
    if (stderr) {
      Logger.warn('Aviso ao listar impressoras', { error: stderr });
    }
    
    let printers = [];
    try {
      const printerData = JSON.parse(stdout);
      // PowerShell pode retornar objeto único ou array
      const printerArray = Array.isArray(printerData) ? printerData : [printerData];
      
      printers = printerArray.map(p => ({
        name: p.Name || p.name,
        status: p.PrinterStatus || 'unknown',
        isDefault: false // Vamos verificar separadamente
      }));
      
      // Obter impressora padrão
      try {
        const defaultCommand = 'powershell -Command "Get-Printer | Where-Object {$_.Default -eq $true} | Select-Object -First 1 -ExpandProperty Name"';
        const { stdout: defaultStdout } = await execAsync(defaultCommand);
        const defaultName = defaultStdout.trim();
        if (defaultName) {
          printers = printers.map(p => ({
            ...p,
            isDefault: p.name === defaultName
          }));
        }
      } catch (error) {
        Logger.debug('Não foi possível obter impressora padrão', { error: error.message });
      }
      
    } catch (parseError) {
      Logger.error('Erro ao parsear lista de impressoras', { error: parseError.message, stdout });
      // Fallback: tentar método alternativo
      return await listPrintersFallback();
    }
    
    Logger.info('Impressoras listadas', { count: printers.length });
    return printers;
  } catch (error) {
    Logger.error('Erro ao listar impressoras', { error: error.message });
    return await listPrintersFallback();
  }
}

async function listPrintersFallback() {
  try {
    // Método alternativo usando wmic
    const command = 'wmic printer get name,default /format:csv';
    const { stdout } = await execAsync(command);
    
    const lines = stdout.split('\n').filter(line => line.trim() && !line.startsWith('Node'));
    const printers = [];
    let defaultName = null;
    
    lines.forEach(line => {
      const parts = line.split(',');
      if (parts.length >= 3) {
        const name = parts[parts.length - 2]?.trim();
        const isDefault = parts[parts.length - 1]?.trim().toLowerCase() === 'true';
        if (name) {
          printers.push({
            name,
            status: 'unknown',
            isDefault
          });
          if (isDefault) {
            defaultName = name;
          }
        }
      }
    });
    
    // Se não encontrou padrão, marcar primeira como padrão
    if (printers.length > 0 && !defaultName) {
      printers[0].isDefault = true;
    }
    
    return printers;
  } catch (error) {
    Logger.error('Erro no fallback de listagem', { error: error.message });
    return [];
  }
}

function getDefaultPrinter() {
  const config = Config.getConfig();
  return config.printerName || defaultPrinter;
}

function setDefaultPrinter(printerName) {
  defaultPrinter = printerName;
  Config.set('printerName', printerName);
  Logger.info('Impressora padrão alterada', { printer: printerName });
}

function generateEscPosCommands(receiptData) {
  const ESC = '\x1B';
  const GS = '\x1D';
  let commands = '';

  // Reset impressora
  commands += ESC + '@';

  // Configurar página de código (PC850 - Português)
  commands += ESC + 't' + '\x10';

  // Cabeçalho - Centralizado e negrito
  commands += ESC + 'a' + '\x01'; // Centralizar
  commands += ESC + 'E' + '\x01'; // Negrito ON
  commands += ESC + '!' + '\x30'; // Tamanho duplo
  
  const businessName = receiptData.business_name || 'Doceria Delícia';
  commands += businessName + '\n';
  
  // Voltar tamanho normal
  commands += ESC + '!' + '\x00';
  commands += ESC + 'E' + '\x00'; // Negrito OFF
  
  // CNPJ se houver
  if (receiptData.cnpj) {
    commands += ESC + 'a' + '\x01';
    commands += 'CNPJ: ' + receiptData.cnpj + '\n';
  }
  
  // Linha divisória
  commands += ESC + 'a' + '\x00'; // Alinhar esquerda
  commands += '-'.repeat(48) + '\n';
  
  // Número do pedido
  commands += ESC + 'a' + '\x01'; // Centralizar
  commands += ESC + 'E' + '\x01'; // Negrito ON
  const orderNumber = String(receiptData.order_number || '').padStart(6, '0');
  commands += 'PEDIDO #' + orderNumber + '\n';
  commands += ESC + 'E' + '\x00'; // Negrito OFF
  
  // Data
  if (receiptData.date) {
    commands += receiptData.date + '\n';
  }
  
  commands += ESC + 'a' + '\x00'; // Alinhar esquerda
  commands += '-'.repeat(48) + '\n';
  
  // Cliente
  if (receiptData.customer_name) {
    commands += 'Cliente: ' + receiptData.customer_name + '\n';
  }
  if (receiptData.customer_phone) {
    commands += 'Tel: ' + receiptData.customer_phone + '\n';
  }
  if (receiptData.delivery_address) {
    commands += 'Entrega:\n';
    // Quebrar endereço em linhas de 48 caracteres
    const address = receiptData.delivery_address;
    for (let i = 0; i < address.length; i += 48) {
      commands += address.substring(i, i + 48) + '\n';
    }
  }
  
  commands += '-'.repeat(48) + '\n';
  
  // Itens
  if (receiptData.items && receiptData.items.length > 0) {
    receiptData.items.forEach(item => {
      const qty = item.quantity + 'x';
      const name = (item.name || '').substring(0, 30);
      const subtotal = formatMoney(item.subtotal || 0);
      
      // Linha 1: quantidade e nome
      commands += qty + ' ' + name + '\n';
      
      // Linha 2: preço unitário e subtotal (alinhado à direita)
      const unitPrice = formatMoney(item.price || 0);
      const unitPriceText = 'R$ ' + unitPrice + '/un';
      const subtotalText = 'R$ ' + subtotal;
      
      // Alinhar à direita
      const padding = Math.max(0, 48 - unitPriceText.length - subtotalText.length - 3);
      commands += ' '.repeat(padding) + unitPriceText + ' = ' + subtotalText + '\n';
    });
  }
  
  commands += '-'.repeat(48) + '\n';
  
  // Totais
  if (receiptData.subtotal !== undefined) {
    commands += 'Subtotal: R$ ' + formatMoney(receiptData.subtotal) + '\n';
  }
  
  if (receiptData.discount && receiptData.discount > 0) {
    commands += 'Desconto: R$ ' + formatMoney(receiptData.discount) + '\n';
  }
  
  if (receiptData.delivery_fee && receiptData.delivery_fee > 0) {
    commands += 'Taxa Entrega: R$ ' + formatMoney(receiptData.delivery_fee) + '\n';
  }
  
  // Total em destaque
  commands += ESC + 'a' + '\x01'; // Centralizar
  commands += ESC + 'E' + '\x01'; // Negrito ON
  commands += ESC + '!' + '\x30'; // Tamanho duplo
  commands += 'TOTAL: R$ ' + formatMoney(receiptData.total || 0) + '\n';
  commands += ESC + '!' + '\x00'; // Tamanho normal
  commands += ESC + 'E' + '\x00'; // Negrito OFF
  
  commands += ESC + 'a' + '\x00'; // Alinhar esquerda
  
  // Forma de pagamento
  if (receiptData.payment_methods_split && receiptData.payment_methods_split.length > 0) {
    commands += 'PAGAMENTO DIVIDIDO:\n';
    receiptData.payment_methods_split.forEach(split => {
      const methodName = getPaymentMethodName(split.method);
      const value = formatMoney(split.value || 0);
      commands += '  ' + methodName + ': R$ ' + value + '\n';
      
      if (split.method === 'dinheiro' && split.change_amount > 0) {
        const received = formatMoney(split.amount_received || 0);
        const change = formatMoney(split.change_amount);
        commands += '    Recebido: R$ ' + received + '\n';
        commands += '    Troco: R$ ' + change + '\n';
      }
    });
  } else if (receiptData.payment_method) {
    const methodName = getPaymentMethodName(receiptData.payment_method);
    commands += 'Pagamento: ' + methodName + '\n';
    
    if (receiptData.payment_method === 'dinheiro') {
      if (receiptData.amount_received > 0) {
        commands += 'Valor Recebido: R$ ' + formatMoney(receiptData.amount_received) + '\n';
      }
      if (receiptData.change_amount > 0) {
        commands += ESC + 'a' + '\x01'; // Centralizar
        commands += ESC + 'E' + '\x01'; // Negrito ON
        commands += ESC + '!' + '\x30'; // Tamanho duplo
        commands += 'TROCO: R$ ' + formatMoney(receiptData.change_amount) + '\n';
        commands += ESC + '!' + '\x00';
        commands += ESC + 'E' + '\x00';
        commands += ESC + 'a' + '\x00';
      }
    }
  }
  
  // Tipo do pedido
  if (receiptData.order_type) {
    const typeName = receiptData.order_type === 'delivery' ? 'Entrega' : 'Balcão';
    commands += 'Tipo: ' + typeName + '\n';
  }
  
  commands += '-'.repeat(48) + '\n';
  
  // Rodapé
  commands += ESC + 'a' + '\x01'; // Centralizar
  const footer = receiptData.footer || 'Obrigado pela preferência!';
  commands += footer + '\n\n';
  
  // Data de impressão
  commands += ESC + 'a' + '\x00'; // Alinhar esquerda
  const now = new Date();
  const printDate = now.toLocaleDateString('pt-BR') + ' ' + 
                    now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
  commands += 'Impresso em: ' + printDate + '\n';
  
  // Espaço e corte
  commands += '\n\n\n';
  commands += GS + 'V' + '\x00'; // Corte total
  
  return commands;
}

function formatMoney(value) {
  return parseFloat(value || 0).toFixed(2).replace('.', ',');
}

function getPaymentMethodName(method) {
  const methods = {
    'dinheiro': 'Dinheiro',
    'cartao_credito': 'Cartão Crédito',
    'cartao_debito': 'Cartão Débito',
    'pix': 'PIX',
    'transferencia': 'Transferência',
    'boleto': 'Boleto'
  };
  return methods[method] || method;
}

function cleanupTempFile(filePath) {
  try {
    if (filePath && fs.existsSync(filePath)) {
      fs.unlinkSync(filePath);
    }
  } catch (error) {
    Logger.warn('Erro ao limpar arquivo temporário', { error: error.message });
  }
}

async function printRaw(printerName, data) {
  return new Promise(async (resolve, reject) => {
    let tempFile = null;
    
    try {
      // Criar arquivo temporário com os dados ESC/POS
      const tempDir = path.join(os.tmpdir(), 'doceria-printer-agent');
      if (!fs.existsSync(tempDir)) {
        fs.mkdirSync(tempDir, { recursive: true });
      }
      
      tempFile = path.join(tempDir, `print_${Date.now()}.raw`);
      
      // Escrever dados no arquivo como buffer binário
      const buffer = Buffer.from(data, 'binary');
      fs.writeFileSync(tempFile, buffer);
      
      Logger.info('Arquivo temporário criado', { file: tempFile, size: buffer.length });
      
      // Escapar o caminho do arquivo e nome da impressora para PowerShell
      const escapedFile = tempFile.replace(/\\/g, '\\\\').replace(/'/g, "''");
      const escapedPrinter = printerName.replace(/'/g, "''");
      
      // Usar PowerShell para ler arquivo como bytes e enviar para impressora
      // Tentar detectar se é impressora de rede ou local
      const command = `powershell -NoProfile -Command `
        + `"$bytes = [System.IO.File]::ReadAllBytes('${escapedFile}'); `
        + `try { `
        + `  $printer = Get-Printer -Name '${escapedPrinter}' -ErrorAction Stop; `
        + `  $port = $printer.PortName; `
        + `  if ($port -match '^IP_|^TCPIP_') { `
        + `    $ip = ($port -replace '^IP_|^TCPIP_', '').Split(':')[0]; `
        + `    $portNum = if (($port -split ':').Count -gt 1) { ($port -split ':')[1] } else { 9100 }; `
        + `    $socket = New-Object System.Net.Sockets.TcpClient($ip, $portNum); `
        + `    $stream = $socket.GetStream(); `
        + `    $stream.Write($bytes, 0, $bytes.Length); `
        + `    $stream.Flush(); `
        + `    $stream.Close(); `
        + `    $socket.Close(); `
        + `    Write-Host 'Enviado via rede para ' + $ip + ':' + $portNum `
        + `  } else { `
        + `    $bytes | Out-Printer -Name '${escapedPrinter}'; `
        + `    Write-Host 'Enviado via Windows' `
        + `  } `
        + `} catch { `
        + `  Write-Error $_.Exception.Message; `
        + `  exit 1 `
        + `}"`;
      
      exec(command, { maxBuffer: 10 * 1024 * 1024 }, (error, stdout, stderr) => {
        // Limpar arquivo temporário
        cleanupTempFile(tempFile);
        
        if (error) {
          Logger.error('Erro ao imprimir via PowerShell', { 
            error: error.message, 
            stderr,
            stdout,
            printer: printerName 
          });
          reject(new Error(`Erro ao imprimir: ${error.message || stderr}`));
          return;
        }
        
        Logger.info('Impressão enviada com sucesso', { printer: printerName, method: stdout.trim() });
        resolve({ success: true });
      });
    } catch (error) {
      if (tempFile) {
        cleanupTempFile(tempFile);
      }
      Logger.error('Erro ao preparar impressão', { error: error.message });
      reject(error);
    }
  });
}

async function printReceipt(receiptData) {
  const printerName = getDefaultPrinter();
  
  if (!printerName) {
    throw new Error('Nenhuma impressora configurada');
  }
  
  Logger.info('Iniciando impressão', { printer: printerName, order: receiptData.order_number });
  
  try {
    const escPosCommands = generateEscPosCommands(receiptData);
    await printRaw(printerName, escPosCommands);
    Logger.info('Impressão concluída com sucesso', { printer: printerName });
    return { success: true };
  } catch (error) {
    Logger.error('Erro ao imprimir recibo', { error: error.message });
    throw error;
  }
}

async function printTestReceipt() {
  const testData = {
    business_name: 'Doceria Delícia',
    order_number: 'TESTE',
    date: new Date().toLocaleDateString('pt-BR') + ' ' + 
          new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
    items: [
      { name: 'Brigadeiro Tradicional', quantity: 2, price: 3.50, subtotal: 7.00 },
      { name: 'Beijinho', quantity: 3, price: 3.50, subtotal: 10.50 }
    ],
    subtotal: 17.50,
    discount: 0,
    delivery_fee: 0,
    total: 17.50,
    payment_method: 'pix',
    order_type: 'balcao',
    footer: 'TESTE DE IMPRESSÃO - Doceria Printer Agent OK!'
  };
  
  return await printReceipt(testData);
}

module.exports = {
  initializePrinterManager,
  listPrinters,
  getDefaultPrinter,
  setDefaultPrinter,
  printReceipt,
  printTestReceipt,
  printRaw
};
