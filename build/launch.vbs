Option Explicit

Const BTN_YES = 6
Const BTN_NO = 7
Const MB_YESNO = 4
Const MB_ICONQUESTION = 32
Const MB_ICONERROR = 16

Dim shell, fso, appDir, launcher, logFile, exitCode, modePrompt, userChoice, cmd
Dim configDir, modeFile, modeValue, modeArg

Set shell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

appDir = fso.GetParentFolderName(WScript.ScriptFullName)
launcher = appDir & "\scripts\start-server.bat"
logFile = appDir & "\logs\startup.log"
configDir = appDir & "\config"
modeFile = configDir & "\startup-mode.txt"

If Not fso.FileExists(launcher) Then
    MsgBox "Enapel Server launcher script was not found:" & vbCrLf & launcher, MB_ICONERROR, "Enapel Server"
    WScript.Quit 1
End If

modePrompt = "Run Enapel Server in NETWORK mode?" & vbCrLf & vbCrLf & _
             "Yes = expose server to other devices on same LAN" & vbCrLf & _
             "No = local computer only"

userChoice = MsgBox(modePrompt, MB_YESNO + MB_ICONQUESTION, "Enapel Server Startup Mode")

If userChoice = BTN_YES Then
    modeValue = "network"
    modeArg = " --network"
Else
    modeValue = "local"
    modeArg = " --local"
End If

If Not fso.FolderExists(configDir) Then
    fso.CreateFolder configDir
End If

With fso.CreateTextFile(modeFile, True)
    .Write modeValue
    .Close
End With

cmd = """" & launcher & """" & modeArg

shell.CurrentDirectory = appDir
exitCode = shell.Run(cmd, 0, True)

If exitCode <> 0 Then
    MsgBox "Enapel Server could not start." & vbCrLf & vbCrLf & _
           "Check this log for details:" & vbCrLf & logFile, _
           MB_ICONERROR, "Enapel Server"
End If

Set fso = Nothing
Set shell = Nothing
