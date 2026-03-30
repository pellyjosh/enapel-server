[Setup]
AppName=Enapel Server
AppVersion=1.0
DefaultDirName={localappdata}\Enapel Server
DefaultGroupName=Enapel Server
OutputDir=..\dist
OutputBaseFilename=enapel-server
Compression=lzma
SolidCompression=yes
PrivilegesRequired=admin
WizardStyle=modern
DisableProgramGroupPage=yes
ArchitecturesAllowed=x64compatible
ArchitecturesInstallIn64BitMode=x64compatible

[Files]
Source: "package\support\vc_redist.x64.exe"; DestDir: "{tmp}"; Flags: deleteafterinstall
Source: "package\app\*"; DestDir: "{app}\app"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "package\php\*"; DestDir: "{app}\php"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "package\scripts\*"; DestDir: "{app}\scripts"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "package\launch.vbs"; DestDir: "{app}"; Flags: ignoreversion
Source: "package\launch-background.vbs"; DestDir: "{app}"; Flags: ignoreversion

[Icons]
Name: "{userprograms}\Enapel Server"; Filename: "{app}\launch.vbs"
Name: "{autodesktop}\Enapel Server"; Filename: "{app}\launch.vbs"
Name: "{userstartup}\Enapel Background Server"; Filename: "{app}\launch-background.vbs"

[Run]
Filename: "{tmp}\vc_redist.x64.exe"; Parameters: "/install /quiet /norestart"; StatusMsg: "Installing Microsoft Visual C++ runtime..."; Flags: waituntilterminated runhidden
Filename: "{sys}\netsh.exe"; Parameters: "advfirewall firewall add rule name=""Enapel Server 8000"" dir=in action=allow protocol=TCP localport=8000"; Flags: runhidden ignoreerrors
Filename: "{app}\scripts\init-server.bat"; Flags: runhidden waituntilterminated
Filename: "{app}\launch.vbs"; Description: "Launch Enapel Server now"; Flags: postinstall shellexec

[UninstallRun]
Filename: "{sys}\netsh.exe"; Parameters: "advfirewall firewall delete rule name=""Enapel Server 8000"""; Flags: runhidden ignoreerrors
