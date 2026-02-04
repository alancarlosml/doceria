; Script personalizado do instalador NSIS
; Este arquivo é incluído automaticamente pelo electron-builder

; Criar entrada no registro para auto-start
!macro customInstall
  ; Criar chave no registro para iniciar com Windows
  WriteRegStr HKCU "Software\Microsoft\Windows\CurrentVersion\Run" "DoceriaPrinterAgent" "$INSTDIR\Doceria Printer Agent.exe"
!macroend

; Remover entrada do registro ao desinstalar
!macro customUnInstall
  DeleteRegValue HKCU "Software\Microsoft\Windows\CurrentVersion\Run" "DoceriaPrinterAgent"
!macroend
