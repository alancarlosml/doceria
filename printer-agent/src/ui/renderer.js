// Carregar configurações ao iniciar
let currentConfig = null;
let printersList = [];

async function loadConfig() {
    try {
        currentConfig = await window.electronAPI.getConfig();
        updateUI();
    } catch (error) {
        console.error('Erro ao carregar configurações:', error);
        showError('Erro ao carregar configurações');
    }
}

async function loadPrinters() {
    try {
        const printers = await window.electronAPI.getPrinters();
        printersList = printers;
        updatePrinterSelect(printers);
    } catch (error) {
        console.error('Erro ao carregar impressoras:', error);
        showError('Erro ao carregar impressoras');
    }
}

function updatePrinterSelect(printers) {
    const select = document.getElementById('printerSelect');
    select.innerHTML = '';
    
    if (printers.length === 0) {
        select.innerHTML = '<option value="">Nenhuma impressora encontrada</option>';
        return;
    }
    
    printers.forEach(printer => {
        const option = document.createElement('option');
        option.value = printer.name;
        option.textContent = printer.name + (printer.isDefault ? ' (Padrão)' : '');
        if (printer.name === currentConfig?.printerName) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}

function updateUI() {
    if (!currentConfig) return;
    
    // Atualizar campos de configuração
    document.getElementById('serverPort').value = currentConfig.serverPort || 8080;
    document.getElementById('autoStart').checked = currentConfig.autoStart !== false;
    document.getElementById('checkUpdates').checked = currentConfig.checkUpdates !== false;
    
    // Atualizar informações
    document.getElementById('appVersion').textContent = '1.0.0';
    document.getElementById('authToken').textContent = currentConfig.authToken || '-';
    
    // Atualizar status
    updateStatus();
}

function updateStatus() {
    const statusCard = document.getElementById('statusCard');
    const statusIndicator = document.getElementById('statusIndicator');
    const statusText = document.getElementById('statusText');
    const statusDetail = document.getElementById('statusDetail');
    const serverStatus = document.getElementById('serverStatus');
    
    if (currentConfig?.printerName) {
        statusIndicator.className = 'status-indicator active';
        statusText.textContent = 'Conectado e Pronto';
        statusDetail.textContent = `Impressora: ${currentConfig.printerName}`;
        serverStatus.textContent = 'Online';
    } else {
        statusIndicator.className = 'status-indicator error';
        statusText.textContent = 'Impressora Não Configurada';
        statusDetail.textContent = 'Configure uma impressora para começar';
        serverStatus.textContent = 'Online (sem impressora)';
    }
}

async function loadLogs() {
    try {
        const logs = await window.electronAPI.getLogs();
        const container = document.getElementById('logsContainer');
        
        if (logs.length === 0) {
            container.innerHTML = '<p class="logs-placeholder">Nenhum log disponível</p>';
            return;
        }
        
        container.innerHTML = logs.map(log => {
            const level = log.includes('[ERROR]') ? 'error' : 
                         log.includes('[WARN]') ? 'warn' : 'info';
            return `<div class="log-entry ${level}">${escapeHtml(log)}</div>`;
        }).join('');
        
        // Scroll para o final
        container.scrollTop = container.scrollHeight;
    } catch (error) {
        console.error('Erro ao carregar logs:', error);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    // Implementar notificação de erro se necessário
    console.error(message);
}

// Event listeners
document.getElementById('printerSelect').addEventListener('change', async (e) => {
    const printerName = e.target.value;
    if (printerName && currentConfig) {
        currentConfig.printerName = printerName;
        await window.electronAPI.saveConfig(currentConfig);
        updateStatus();
    }
});

document.getElementById('refreshPrinters').addEventListener('click', () => {
    loadPrinters();
});

document.getElementById('testPrint').addEventListener('click', async () => {
    const btn = document.getElementById('testPrint');
    const result = document.getElementById('testResult');
    
    btn.disabled = true;
    btn.textContent = 'Imprimindo...';
    result.textContent = '';
    
    try {
        const response = await window.electronAPI.testPrint();
        if (response.success) {
            result.textContent = response.message;
            result.className = 'test-result success';
        } else {
            result.textContent = response.message;
            result.className = 'test-result error';
        }
    } catch (error) {
        result.textContent = 'Erro: ' + error.message;
        result.className = 'test-result error';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Testar Impressão';
    }
});

document.getElementById('serverPort').addEventListener('change', async (e) => {
    const port = parseInt(e.target.value);
    if (currentConfig && port !== currentConfig.serverPort) {
        currentConfig.serverPort = port;
        await window.electronAPI.saveConfig(currentConfig);
    }
});

document.getElementById('autoStart').addEventListener('change', async (e) => {
    if (currentConfig) {
        currentConfig.autoStart = e.target.checked;
        await window.electronAPI.saveConfig(currentConfig);
    }
});

document.getElementById('checkUpdates').addEventListener('change', async (e) => {
    if (currentConfig) {
        currentConfig.checkUpdates = e.target.checked;
        await window.electronAPI.saveConfig(currentConfig);
    }
});

document.getElementById('refreshLogs').addEventListener('click', () => {
    loadLogs();
});

document.getElementById('clearLogs').addEventListener('click', async () => {
    if (confirm('Tem certeza que deseja limpar todos os logs?')) {
        await window.electronAPI.clearLogs();
        loadLogs();
    }
});

document.getElementById('copyToken').addEventListener('click', () => {
    const token = document.getElementById('authToken').textContent;
    navigator.clipboard.writeText(token).then(() => {
        const btn = document.getElementById('copyToken');
        const originalText = btn.textContent;
        btn.textContent = 'Copiado!';
        setTimeout(() => {
            btn.textContent = originalText;
        }, 2000);
    });
});

// Verificar status do servidor periodicamente
setInterval(async () => {
    try {
        const response = await fetch('http://localhost:8080/status');
        const data = await response.json();
        document.getElementById('serverStatus').textContent = 'Online';
    } catch (error) {
        document.getElementById('serverStatus').textContent = 'Offline';
    }
}, 5000);

// Inicializar
loadConfig().then(() => {
    loadPrinters();
    loadLogs();
    
    // Atualizar logs a cada 5 segundos
    setInterval(loadLogs, 5000);
});
