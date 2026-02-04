// Script para criar um ícone simples usando Canvas (se disponível) ou criar um ícone básico
const fs = require('fs');
const path = require('path');
const { nativeImage } = require('electron');

// Criar um ícone simples de impressora usando SVG convertido para PNG
const svgIcon = `
<svg width="256" height="256" xmlns="http://www.w3.org/2000/svg">
  <rect width="256" height="256" fill="#667eea"/>
  <text x="128" y="180" font-size="180" text-anchor="middle" fill="white" font-family="Arial">🖨️</text>
</svg>
`;

try {
  // Tentar criar ícone a partir de SVG
  const icon = nativeImage.createFromDataURL('data:image/svg+xml;base64,' + Buffer.from(svgIcon).toString('base64'));
  
  // Redimensionar para tamanhos padrão
  const sizes = [16, 32, 48, 64, 128, 256];
  const images = sizes.map(size => icon.resize({ width: size, height: size }));
  
  // Salvar como PNG temporário (o Electron pode usar PNG como ícone)
  const iconDir = path.join(__dirname, 'build');
  if (!fs.existsSync(iconDir)) {
    fs.mkdirSync(iconDir, { recursive: true });
  }
  
  // Salvar como PNG (funciona no Electron)
  const pngPath = path.join(iconDir, 'icon.png');
  fs.writeFileSync(pngPath, icon.toPNG());
  
  console.log('Ícone criado em:', pngPath);
  console.log('Nota: Para produção, converta para .ico usando um conversor online');
} catch (error) {
  console.error('Erro ao criar ícone:', error.message);
  console.log('Usando ícone padrão do sistema');
}
