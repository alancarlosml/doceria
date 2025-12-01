/**
 * QZ Tray Integration for Doce Doce Brigaderia
 * 
 * Este módulo gerencia a comunicação com o QZ Tray para impressão
 * direta em impressoras térmicas via ESC/POS
 */

const QZPrint = {
    // Estado da conexão
    connected: false,
    printerName: null,
    
    // Configurações ESC/POS
    ESC: '\x1B',
    GS: '\x1D',
    
    /**
     * Inicializar conexão com QZ Tray
     */
    async init() {
        if (typeof qz === 'undefined') {
            console.error('QZ Tray library not loaded');
            return false;
        }
        
        try {
            // Configurar certificado (para produção, você deve usar um certificado próprio)
            qz.security.setCertificatePromise(function(resolve, reject) {
                // Certificado de desenvolvimento - em produção use um certificado válido
                resolve("-----BEGIN CERTIFICATE-----\n" +
                    "MIIECzCCAvOgAwIBAgIJALSjUGKdXv4LMA0GCSqGSIb3DQEBCwUAMIGXMQswCQYD\n" +
                    "VQQGEwJCUjEPMA0GA1UECAwGQmFoaWExDTALBgNVBAcMBFNTQTEUMBIGA1UECgwL\n" +
                    "RG9jZURvY2VCcjETMBEGA1UECwwKRGVzZW52b2x2MRgwFgYDVQQDDA9kb2NlZG9j\n" +
                    "ZWJyLmNvbTEjMCEGCSqGSIb3DQEJARYUYWRtaW5AZG9jZWRvY2Vici5jb20wHhcN\n" +
                    "MjQwMTAxMDAwMDAwWhcNMzQwMTAxMDAwMDAwWjCBlzELMAkGA1UEBhMCQlIxDzAN\n" +
                    "BgNVBAgMBkJhaGlhMQ0wCwYDVQQHDARTU0ExFDASBgNVBAoMC0RvY2VEb2NlQnIx\n" +
                    "EzARBgNVBAsMCkRlc2Vudm9sdjEYMBYGA1UEAwwPZG9jZWRvY2Vici5jb20xIzAh\n" +
                    "BgkqhkiG9w0BCQEWFGFkbWluQGRvY2Vkb2NlYnIuY29tMIIBIjANBgkqhkiG9w0B\n" +
                    "AQEFAAOCAQ8AMIIBCgKCAQEA0Z3VS5JJcds3xfn/ygWyf8X3tjJJQ3Ij1jVWLf1E\n" +
                    "xL0mBbEP0RRnJxFYITKQGZhJJDIlQpJ5FQ5Q0VBYxL3Hl3LM5J3pD3JL1JT5VYJT\n" +
                    "Hl0QJ0JqDQU0Q0VJU0VQ0Q0VJU0VQ0Q0VJU0VQ0Q0VJU0VQ0Q0VJU0VQ0Q0VJUV\n" +
                    "Q0Q0VJU0VQ0Q0VJU0VQ0Q0VJU0VQ0Q0VJU0VQ0Q0VJU0VQ0Q0VJU0VQ0Q0VJU0V\n" +
                    "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA\n" +
                    "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA\n" +
                    "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIBAAABMA0GCSqGSIb3DQEBCwUAA4IB\n" +
                    "-----END CERTIFICATE-----");
            });
            
            // Signature (em produção, use sua própria chave privada)
            qz.security.setSignatureAlgorithm("SHA512");
            qz.security.setSignaturePromise(function(toSign) {
                return function(resolve, reject) {
                    // Em desenvolvimento, podemos usar uma assinatura vazia
                    // Em produção, você deve assinar com sua chave privada
                    resolve("");
                };
            });
            
            // Conectar ao QZ Tray
            if (!qz.websocket.isActive()) {
                await qz.websocket.connect();
            }
            
            this.connected = true;
            console.log('✅ QZ Tray conectado com sucesso!');
            return true;
            
        } catch (error) {
            console.error('❌ Erro ao conectar ao QZ Tray:', error);
            this.connected = false;
            return false;
        }
    },
    
    /**
     * Verificar se está conectado
     */
    isConnected() {
        return this.connected && qz.websocket.isActive();
    },
    
    /**
     * Listar impressoras disponíveis
     */
    async listPrinters() {
        if (!this.isConnected()) {
            await this.init();
        }
        
        try {
            const printers = await qz.printers.find();
            console.log('Impressoras encontradas:', printers);
            return printers;
        } catch (error) {
            console.error('Erro ao listar impressoras:', error);
            return [];
        }
    },
    
    /**
     * Definir impressora padrão
     */
    setPrinter(name) {
        this.printerName = name;
        localStorage.setItem('qz_printer_name', name);
        console.log('Impressora definida:', name);
    },
    
    /**
     * Obter impressora configurada
     */
    getPrinter() {
        if (!this.printerName) {
            this.printerName = localStorage.getItem('qz_printer_name');
        }
        return this.printerName;
    },
    
    /**
     * Gerar comandos ESC/POS para recibo de venda
     */
    generateReceiptCommands(receiptData) {
        const ESC = '\x1B';
        const GS = '\x1D';
        const commands = [];
        
        // Inicializar impressora
        commands.push(ESC + '@'); // Reset
        
        // Configurar página de código para caracteres portugueses
        commands.push(ESC + 't' + '\x10'); // PC850 - Multilingual Latin I
        
        // Centralizar e negrito para cabeçalho
        commands.push(ESC + 'a' + '\x01'); // Centralizar
        commands.push(ESC + 'E' + '\x01'); // Negrito ON
        commands.push(ESC + '!' + '\x30'); // Tamanho duplo
        
        // Nome da empresa
        commands.push(receiptData.business_name || 'Doce Doce Brigaderia');
        commands.push('\n');
        
        // Voltar tamanho normal
        commands.push(ESC + '!' + '\x00');
        commands.push(ESC + 'E' + '\x00'); // Negrito OFF
        
        // Linha divisória
        commands.push('-'.repeat(48) + '\n');
        
        // Número do pedido
        commands.push(ESC + 'E' + '\x01'); // Negrito ON
        commands.push('PEDIDO #' + String(receiptData.order_number).padStart(6, '0') + '\n');
        commands.push(ESC + 'E' + '\x00'); // Negrito OFF
        
        // Data
        commands.push(receiptData.date + '\n');
        
        // Linha divisória
        commands.push('-'.repeat(48) + '\n');
        
        // Alinhar à esquerda para itens
        commands.push(ESC + 'a' + '\x00');
        
        // Cliente (se houver)
        if (receiptData.customer_name) {
            commands.push('Cliente: ' + receiptData.customer_name + '\n');
        }
        if (receiptData.customer_phone) {
            commands.push('Tel: ' + receiptData.customer_phone + '\n');
        }
        if (receiptData.delivery_address) {
            commands.push('Entrega:\n');
            // Quebrar endereço em linhas de 48 caracteres
            const address = receiptData.delivery_address;
            for (let i = 0; i < address.length; i += 48) {
                commands.push(address.substring(i, i + 48) + '\n');
            }
        }
        
        commands.push('-'.repeat(48) + '\n');
        
        // Itens
        receiptData.items.forEach(item => {
            const qty = item.quantity + 'x';
            const name = item.name.substring(0, 30);
            const price = 'R$ ' + this.formatMoney(item.subtotal);
            
            // Linha 1: quantidade e nome
            commands.push(qty + ' ' + name + '\n');
            
            // Linha 2: preço unitário e subtotal (alinhado à direita)
            const unitPrice = 'R$ ' + this.formatMoney(item.price) + '/un = ' + price;
            commands.push(' '.repeat(48 - unitPrice.length) + unitPrice + '\n');
        });
        
        commands.push('-'.repeat(48) + '\n');
        
        // Totais
        if (receiptData.subtotal) {
            const subtotalLine = 'Subtotal: R$ ' + this.formatMoney(receiptData.subtotal);
            commands.push(subtotalLine + '\n');
        }
        
        if (receiptData.discount && receiptData.discount > 0) {
            const discountLine = 'Desconto: -R$ ' + this.formatMoney(receiptData.discount);
            commands.push(discountLine + '\n');
        }
        
        if (receiptData.delivery_fee && receiptData.delivery_fee > 0) {
            const feeLine = 'Taxa Entrega: R$ ' + this.formatMoney(receiptData.delivery_fee);
            commands.push(feeLine + '\n');
        }
        
        // Total em destaque
        commands.push(ESC + 'a' + '\x01'); // Centralizar
        commands.push(ESC + 'E' + '\x01'); // Negrito ON
        commands.push(ESC + '!' + '\x30'); // Tamanho duplo
        commands.push('TOTAL: R$ ' + this.formatMoney(receiptData.total) + '\n');
        commands.push(ESC + '!' + '\x00'); // Tamanho normal
        commands.push(ESC + 'E' + '\x00'); // Negrito OFF
        
        commands.push(ESC + 'a' + '\x00'); // Alinhar esquerda
        
        // Forma de pagamento
        if (receiptData.payment_method) {
            commands.push('Pagamento: ' + this.getPaymentMethodName(receiptData.payment_method) + '\n');
        }
        
        // Tipo do pedido
        if (receiptData.order_type) {
            const typeName = receiptData.order_type === 'delivery' ? 'Entrega' : 'Balcao';
            commands.push('Tipo: ' + typeName + '\n');
        }
        
        commands.push('-'.repeat(48) + '\n');
        
        // Rodapé
        commands.push(ESC + 'a' + '\x01'); // Centralizar
        commands.push(receiptData.footer || 'Obrigado pela preferencia!' + '\n');
        commands.push('\n');
        
        // Data de impressão
        commands.push(ESC + 'a' + '\x00'); // Alinhar esquerda
        const now = new Date();
        const printDate = now.toLocaleDateString('pt-BR') + ' ' + now.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
        commands.push('Impresso em: ' + printDate + '\n');
        
        // Espaço e corte
        commands.push('\n\n\n');
        commands.push(GS + 'V' + '\x00'); // Corte total
        
        return commands.join('');
    },
    
    /**
     * Formatar valor monetário
     */
    formatMoney(value) {
        return parseFloat(value || 0).toFixed(2).replace('.', ',');
    },
    
    /**
     * Obter nome do método de pagamento
     */
    getPaymentMethodName(method) {
        const methods = {
            'dinheiro': 'Dinheiro',
            'cartao_credito': 'Cartao Credito',
            'cartao_debito': 'Cartao Debito',
            'pix': 'PIX',
            'transferencia': 'Transferencia',
            'boleto': 'Boleto'
        };
        return methods[method] || method;
    },
    
    /**
     * Imprimir recibo de venda
     */
    async printReceipt(receiptData) {
        if (!this.isConnected()) {
            const connected = await this.init();
            if (!connected) {
                throw new Error('Não foi possível conectar ao QZ Tray. Verifique se está instalado e rodando.');
            }
        }
        
        const printerName = this.getPrinter();
        if (!printerName) {
            throw new Error('Nenhuma impressora configurada. Configure nas configurações do sistema.');
        }
        
        try {
            // Gerar comandos ESC/POS
            const commands = this.generateReceiptCommands(receiptData);
            
            // Configurar impressão
            const config = qz.configs.create(printerName, {
                encoding: 'ISO-8859-1'
            });
            
            // Enviar para impressora
            const data = [{
                type: 'raw',
                format: 'plain',
                data: commands
            }];
            
            await qz.print(config, data);
            console.log('✅ Recibo impresso com sucesso!');
            return true;
            
        } catch (error) {
            console.error('❌ Erro ao imprimir:', error);
            throw error;
        }
    },
    
    /**
     * Imprimir cupom de teste
     */
    async printTest() {
        const testData = {
            business_name: 'Doce Doce Brigaderia',
            order_number: 'TESTE',
            date: new Date().toLocaleDateString('pt-BR') + ' ' + new Date().toLocaleTimeString('pt-BR'),
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
            footer: 'TESTE DE IMPRESSAO - QZ Tray OK!'
        };
        
        return await this.printReceipt(testData);
    },
    
    /**
     * Desconectar do QZ Tray
     */
    async disconnect() {
        if (qz.websocket.isActive()) {
            await qz.websocket.disconnect();
            this.connected = false;
            console.log('QZ Tray desconectado');
        }
    }
};

// Exportar para uso global
window.QZPrint = QZPrint;

