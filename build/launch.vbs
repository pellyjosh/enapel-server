Option Explicit

Const BTN_YES = 6
Const BTN_NO = 7
Const MB_YESNO = 4
Const MB_ICONQUESTION = 32
Const MB_ICONERROR = 16
Const FOR_APPENDING = 8

Dim shell, fso, appDir, launcher, logFile, exitCode, modePrompt, userChoice, cmd
Dim configDir, modeFile, modeValue, modeArg
Dim browserUrl, logDir

Set shell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

appDir = fso.GetParentFolderName(WScript.ScriptFullName)
launcher = appDir & "\scripts\start-server.bat"
logFile = appDir & "\logs\startup.log"
logDir = appDir & "\logs"
configDir = appDir & "\config"
modeFile = configDir & "\startup-mode.txt"
browserUrl = "http://127.0.0.1:8000"

If Not fso.FolderExists(logDir) Then
    fso.CreateFolder logDir
End If

LogMessage "launch.vbs invoked."

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

cmd = "cmd /c """"" & launcher & """" & modeArg & " --no-browser"""

LogMessage "Selected mode: " & modeValue
LogMessage "Executing: " & cmd

shell.CurrentDirectory = appDir

Dim splashProc
Set splashProc = Nothing
If fso.FileExists(appDir & "\scripts\splash.hta") Then
    Set splashProc = shell.Exec("mshta """ & appDir & "\scripts\splash.hta""")
End If

On Error Resume Next
exitCode = shell.Run(cmd, 0, True)

If Not splashProc Is Nothing Then
    splashProc.Terminate()
End If

If Err.Number <> 0 Then
    LogMessage "Failed to execute start-server command: " & Err.Description
    MsgBox "Failed to run launcher command." & vbCrLf & vbCrLf & _
           Err.Description & vbCrLf & vbCrLf & _
           "Check this log for details:" & vbCrLf & logFile, _
           MB_ICONERROR, "Enapel Server"
    WScript.Quit 1
End If
On Error GoTo 0

LogMessage "start-server exit code: " & CStr(exitCode)

If exitCode = 0 Then
    On Error Resume Next
    LogMessage "Opening browser at " & browserUrl
    shell.Run browserUrl, 1, False
    If Err.Number <> 0 Then
        LogMessage "Primary browser open failed: " & Err.Description
        Err.Clear
        shell.Run "explorer.exe """ & browserUrl & """", 1, False
        If Err.Number <> 0 Then
            LogMessage "Fallback browser open failed: " & Err.Description
        Else
            LogMessage "Fallback browser open command launched."
        End If
    Else
        LogMessage "Browser open command launched."
    End If
    On Error GoTo 0
Else
    MsgBox "Enapel Server could not start." & vbCrLf & vbCrLf & _
           "Exit code: " & CStr(exitCode) & vbCrLf & vbCrLf & _
           "Check this log for details:" & vbCrLf & logFile, _
           MB_ICONERROR, "Enapel Server"
End If

Set fso = Nothing
Set shell = Nothing

Sub LogMessage(message)
    On Error Resume Next
    Dim stream
    Set stream = fso.OpenTextFile(logFile, FOR_APPENDING, True)
    stream.WriteLine "[" & Now & "] " & message
    stream.Close
    Set stream = Nothing
    On Error GoTo 0
End Sub
