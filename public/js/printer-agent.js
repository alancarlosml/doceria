/**
 * Printer Agent Integration
 * Comunicação com o Doceria Printer Agent (executável local)
 */

const PrinterAgent = {
    baseUrl: 'http://localhost:8080',
    timeout: 5000,
    isAvailable: false,
    status: null,

    /**
     * Verificar se o agente está rodando
     */
    async checkStatus() {
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), this.timeout);

            const response = await fetch(`${this.baseUrl}/status`, {
                signal: controller.signal,
                method: 'GET'
            });

            clearTimeout(timeoutId);

            if (response.ok) {
                const data = await response.json();
                this.isAvailable = data.status === 'running';
                this.status = data;
                return this.isAvailable;
            }

            this.isAvailable = false;
            return false;
        } catch (error) {
            this.isAvailable = false;
            this.status = null;
            return false;
        }
    },

    /**
     * Listar impressoras disponíveis
     */
    async getPrinters() {
        try {
            const response = await fetch(`${this.baseUrl}/printers`);
            if (response.ok) {
                const data = await response.json();
                return data.printers || [];
            }
            return [];
        } catch (error) {
            console.error('Erro ao listar impressoras via agente:', error);
            return [];
        }
    },

    /**
     * Enviar comando de impressão para o agente
     */
    async printReceipt(receiptData) {
        try {
            const response = await fetch(`${this.baseUrl}/print`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(receiptData)
            });

            if (response.ok) {
                const data = await response.json();
                return {
                    success: data.success || false,
                    message: data.message || 'Impressão enviada com sucesso'
                };
            } else {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || 'Erro ao imprimir');
            }
        } catch (error) {
            console.error('Erro ao imprimir via agente:', error);
            throw error;
        }
    },

    /**
     * Configurar impressora padrão
     */
    async setPrinter(printerName) {
        try {
            const response = await fetch(`${this.baseUrl}/config`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ printerName })
            });

            if (response.ok) {
                const data = await response.json();
                return data.success || false;
            }
            return false;
        } catch (error) {
            console.error('Erro ao configurar impressora:', error);
            return false;
        }
    },

    /**
     * Obter configuração do agente
     */
    async getConfig() {
        try {
            const response = await fetch(`${this.baseUrl}/config`);
            if (response.ok) {
                const data = await response.json();
                return data.config || null;
            }
            return null;
        } catch (error) {
            console.error('Erro ao obter configuração:', error);
            return null;
        }
    },

    /**
     * Verificar status via API do Laravel (fallback)
     */
    async checkStatusViaAPI() {
        try {
            const response = await fetch('/gestor/api/printer/agent/status', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.isAvailable = data.running || false;
                this.status = data.status || null;
                return this.isAvailable;
            }

            this.isAvailable = false;
            return false;
        } catch (error) {
            this.isAvailable = false;
            return false;
        }
    },

    /**
     * Imprimir recibo de venda (método completo)
     * Tenta usar o agente primeiro, depois fallback para QZ Tray
     */
    async printSaleReceipt(saleId) {
        // Tentar usar agente primeiro
        const agentAvailable = await this.checkStatus();
        
        if (agentAvailable) {
            try {
                // Buscar dados do recibo
                const response = await fetch(`/gestor/vendas/${saleId}/receipt-data`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Erro ao obter dados do recibo');
                }

                // Enviar para o agente
                const result = await this.printReceipt(data.receipt);
                return {
                    success: true,
                    method: 'agent',
                    message: result.message || 'Impressão via agente concluída'
                };
            } catch (error) {
                console.warn('Erro ao imprimir via agente, tentando fallback:', error);
                // Continuar para fallback
            }
        }

        // Fallback: tentar QZ Tray se disponível
        if (typeof QZPrint !== 'undefined') {
            try {
                if (!QZPrint.isConnected()) {
                    const connected = await Promise.race([
                        QZPrint.init(),
                        new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 3000))
                    ]);
                    if (!connected) {
                        throw new Error('QZ Tray não conectado');
                    }
                }

                const printerName = QZPrint.getPrinter();
                if (!printerName) {
                    throw new Error('Nenhuma impressora configurada no QZ Tray');
                }

                const response = await fetch(`/gestor/vendas/${saleId}/receipt-data`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Erro ao obter dados do recibo');
                }

                await QZPrint.printReceipt(data.receipt);
                return {
                    success: true,
                    method: 'qz-tray',
                    message: 'Impressão via QZ Tray concluída'
                };
            } catch (error) {
                console.warn('Erro ao imprimir via QZ Tray:', error);
                // Continuar para último fallback
            }
        }

        // Último fallback: impressão via servidor PHP
        try {
            const response = await fetch(`/gestor/vendas/${saleId}/imprimir-recibo`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const data = await response.json();
            if (data.success) {
                return {
                    success: true,
                    method: 'server',
                    message: data.message || 'Impressão via servidor concluída'
                };
            } else {
                throw new Error(data.message || 'Erro ao imprimir');
            }
        } catch (error) {
            return {
                success: false,
                method: 'none',
                message: error.message || 'Erro ao imprimir recibo'
            };
        }
    }
};

// Exportar para uso global
window.PrinterAgent = PrinterAgent;

// Verificar status do agente periodicamente (a cada 30 segundos)
setInterval(() => {
    PrinterAgent.checkStatus().catch(() => {
        // Silenciosamente falhar
    });
}, 30000);

// Verificar status na inicialização
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        PrinterAgent.checkStatus();
    });
} else {
    PrinterAgent.checkStatus();
}
