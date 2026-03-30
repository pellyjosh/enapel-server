Option Explicit

Dim shell, fso, appDir, launcher, modeFile, modeArg, savedMode

Set shell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

appDir = fso.GetParentFolderName(WScript.ScriptFullName)
launcher = appDir & "\scripts\start-server.bat"
modeFile = appDir & "\config\startup-mode.txt"
modeArg = " --local"

If fso.FileExists(launcher) Then
    If fso.FileExists(modeFile) Then
        savedMode = LCase(Trim(fso.OpenTextFile(modeFile, 1).ReadAll))
        If savedMode = "network" Then
            modeArg = " --network"
        End If
    End If

    shell.CurrentDirectory = appDir
    shell.Run """" & launcher & """ --no-browser" & modeArg, 0, False
End If

Set fso = Nothing
Set shell = Nothing
