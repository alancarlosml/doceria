// Script para criar um ícone melhor para o tray
// Execute: node create-tray-icon.js

const fs = require('fs');
const path = require('path');
const { nativeImage } = require('electron');

// Criar um ícone SVG melhorado (16x16)
const svgIcon = `<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="16" height="16" fill="url(#grad)" rx="2"/>
  <text x="8" y="12" font-size="10" font-weight="bold" text-anchor="middle" fill="white" font-family="Arial, sans-serif">P</text>
</svg>`;

try {
  const icon = nativeImage.createFromDataURL('data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgIcon));
  
  // Salvar como PNG para referência
  const buildDir = path.join(__dirname, 'build');
  if (!fs.existsSync(buildDir)) {
    fs.mkdirSync(buildDir, { recursive: true });
  }
  
  const pngPath = path.join(buildDir, 'tray-icon.png');
  fs.writeFileSync(pngPath, icon.toPNG());
  
  console.log('Ícone do tray criado em:', pngPath);
  console.log('Nota: Para usar este ícone, converta para .ico usando:');
  console.log('  - https://convertio.co/png-ico/');
  console.log('  - https://www.icoconverter.com/');
  console.log('E salve como build/icon.ico');
} catch (error) {
  console.error('Erro ao criar ícone:', error.message);
}
