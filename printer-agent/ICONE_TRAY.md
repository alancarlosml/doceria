# Como Criar um Ícone para o Tray

## Problema

O ícone aparece normal na instalação, mas na bandeja do sistema aparece um quadrado verde (ícone temporário).

## Solução

O código agora tenta usar o ícone do executável automaticamente quando disponível. Mas para ter um ícone personalizado melhor:

### Opção 1: Criar Ícone Personalizado

1. Crie uma imagem 16x16 ou 32x32 pixels (PNG)
2. Use uma cor de fundo (#667eea - roxo) com uma letra "P" branca ou símbolo de impressora
3. Converta para .ico usando:
   - https://convertio.co/png-ico/
   - https://www.icoconverter.com/
4. Salve como `build/icon.ico`

### Opção 2: Usar o Ícone Gerado

Execute:
```bash
node create-tray-icon.js
```

Isso criará `build/tray-icon.png`. Converta para .ico e salve como `build/icon.ico`.

### Opção 3: Usar Ícone do Executável

Quando o aplicativo é compilado, o código tenta automaticamente usar o ícone do executável. Se você configurou um ícone no `package.json` (build.win.icon), ele será usado.

## Verificação

Após criar o ícone:
1. Rebuild o aplicativo: `npm run build:win`
2. Instale a nova versão
3. O ícone deve aparecer corretamente na bandeja

## Nota

O código foi corrigido para tentar usar o ícone do executável primeiro quando o app está empacotado, o que deve resolver o problema do quadrado verde.
