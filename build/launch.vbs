Set WshShell = CreateObject("WScript.Shell")
' Run the batch file in hidden mode (0)
WshShell.Run chr(34) & "scripts\start-server.bat" & Chr(34), 0
Set WshShell = Nothing
