import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { formatDistanceToNow } from 'date-fns';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TablePlaceholder from '@/Components/TablePlaceholder';

function formatAgo(value) {
    if (!value) {
        return 'Never';
    }

    return formatDistanceToNow(new Date(value), { addSuffix: true });
}

function SectionCard({ title, subtitle, children }) {
    return (
        <div className="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-xl shadow-gray-200/40">
            <h2 className="text-2xl font-black text-gray-900">{title}</h2>
            {subtitle && <p className="text-gray-500 font-medium mt-2">{subtitle}</p>}
            <div className="mt-5 space-y-5">{children}</div>
        </div>
    );
}

function HelpText({ children }) {
    return <p className="text-sm text-gray-500 leading-6">{children}</p>;
}

function inputClass(hasError, extra = '') {
    return `w-full rounded-2xl border ${hasError ? 'border-rose-500 bg-rose-50 focus:border-rose-500 focus:ring-rose-200' : 'border-gray-200'} ${extra}`.trim();
}

function detectOperatingSystem() {
    if (typeof window === 'undefined') {
        return 'windows';
    }

    const agent = `${window.navigator.userAgent} ${window.navigator.platform}`.toLowerCase();

    if (agent.includes('mac')) {
        return 'mac';
    }

    if (agent.includes('linux') || agent.includes('x11')) {
        return 'linux';
    }

    return 'windows';
}

function OperatingSystemIcon({ os }) {
    if (os === 'mac') {
        return (
            <svg viewBox="0 0 24 24" fill="none" className="h-6 w-6" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
                <path d="M9 18c.4-1.2.8-2.3 1.7-3.4.8-1 1.8-1.8 3.3-2.5" />
                <path d="M14.8 7.3c.8-1 1.1-2.2 1-3.3-1.1.1-2.3.8-3 1.7-.7.8-1.2 2-1.1 3.2 1.2.1 2.4-.5 3.1-1.6Z" />
                <path d="M8.2 8.7c1-.9 2.4-1.4 3.6-1.4 1 0 2 .3 2.8.8.5.3 1 .7 1.6.7.5 0 1-.3 1.5-.5.6-.3 1.3-.5 2-.4-.2.7-.8 1.4-1.3 1.9-.6.5-1 1.2-1 2 0 1 .5 1.9 1.3 2.5-.5 1.3-1 2.2-1.8 3.1-.8.9-1.6 1.8-2.9 1.8-1.1 0-1.5-.6-2.8-.6-1.2 0-1.7.6-2.8.6-1.2 0-2-.8-2.8-1.7-1.7-2-3-5.6-1.3-8.8.5-.8 1.1-1.5 1.9-2Z" />
            </svg>
        );
    }

    if (os === 'linux') {
        return (
            <svg viewBox="0 0 24 24" fill="none" className="h-6 w-6" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
                <path d="M12 3c2.7 0 4.5 2.6 4.5 5.7 0 1.5-.4 3-1.1 4.1l1.1 3.8c.2.8-.4 1.5-1.2 1.3l-2.2-.5-.9 2.8c-.2.6-1 .6-1.2 0l-.9-2.8-2.2.5c-.8.2-1.5-.5-1.2-1.3l1.1-3.8C7.9 11.7 7.5 10.2 7.5 8.7 7.5 5.6 9.3 3 12 3Z" />
                <path d="M10 10.5h.01" />
                <path d="M14 10.5h.01" />
                <path d="M10 14c1.1.9 2.9.9 4 0" />
            </svg>
        );
    }

    return (
        <svg viewBox="0 0 24 24" fill="none" className="h-6 w-6" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
            <rect x="3" y="4" width="18" height="12" rx="2" />
            <path d="M8 20h8" />
            <path d="M10 16v4" />
            <path d="M14 16v4" />
            <path d="M12 8v4" />
            <path d="M8 12h8" />
        </svg>
    );
}

function SharingPreviewCard({ guide, detectedOs }) {
    const themeClasses = {
        blue: {
            panel: 'border-blue-200 bg-blue-50/80',
            icon: 'bg-blue-600 text-white',
            chip: 'border-blue-200 bg-white text-blue-900',
            focus: 'border-blue-300 bg-white text-blue-950',
        },
        violet: {
            panel: 'border-violet-200 bg-violet-50/80',
            icon: 'bg-violet-600 text-white',
            chip: 'border-violet-200 bg-white text-violet-900',
            focus: 'border-violet-300 bg-white text-violet-950',
        },
        emerald: {
            panel: 'border-emerald-200 bg-emerald-50/80',
            icon: 'bg-emerald-600 text-white',
            chip: 'border-emerald-200 bg-white text-emerald-900',
            focus: 'border-emerald-300 bg-white text-emerald-950',
        },
    };

    const theme = themeClasses[guide.theme] || themeClasses.blue;

    return (
        <div className={`rounded-3xl border p-5 ${theme.panel}`}>
            <div className="flex items-start gap-3">
                <div className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl ${theme.icon}`}>
                    <OperatingSystemIcon os={detectedOs} />
                </div>
                <div>
                    <p className="text-sm font-black text-gray-900">{guide.preview.title}</p>
                    <p className="mt-1 text-sm text-gray-600 leading-6">{guide.preview.subtitle}</p>
                </div>
            </div>

            <div className="mt-4 overflow-hidden rounded-[1.75rem] border border-gray-200 bg-white shadow-sm">
                <div className="flex items-center gap-2 border-b border-gray-200 bg-gray-900 px-4 py-3">
                    <span className="h-2.5 w-2.5 rounded-full bg-rose-400" />
                    <span className="h-2.5 w-2.5 rounded-full bg-amber-400" />
                    <span className="h-2.5 w-2.5 rounded-full bg-emerald-400" />
                    <p className="ml-2 text-[11px] font-bold uppercase tracking-[0.25em] text-gray-300">{guide.preview.windowTitle}</p>
                </div>

                <div className="space-y-4 bg-gradient-to-br from-white via-gray-50 to-gray-100/70 p-4">
                    <div className="flex flex-wrap gap-2">
                        {guide.preview.menuTrail.map((item) => (
                            <span key={item} className={`rounded-full border px-3 py-1 text-xs font-black uppercase tracking-[0.18em] ${theme.chip}`}>
                                {item}
                            </span>
                        ))}
                    </div>

                    <div className={`rounded-2xl border-2 border-dashed px-4 py-4 ${theme.focus}`}>
                        <p className="text-[11px] font-bold uppercase tracking-[0.28em] text-gray-500">Look for this</p>
                        <p className="mt-2 text-lg font-black">{guide.preview.focusLabel}</p>
                        <p className="mt-2 text-sm leading-6 text-gray-600">{guide.preview.focusHint}</p>
                    </div>

                    <div className="grid grid-cols-3 gap-2">
                        {guide.preview.shortcuts.map((item) => (
                            <div key={item} className="rounded-2xl border border-gray-200 bg-white px-3 py-3 text-center text-[11px] font-black uppercase tracking-[0.2em] text-gray-600 shadow-sm">
                                {item}
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}

function SharingHelpModal({ show, onClose, detectedOs }) {
    const [copiedExamplePath, setCopiedExamplePath] = React.useState(false);
    const copyTimeoutRef = React.useRef(null);

    const osGuides = {
        windows: {
            label: 'Windows',
            theme: 'blue',
            intro: 'This is the easiest option for most Enapel office setups.',
            path: '\\COMPUTER-NAME\EnapelBackups',
            preview: {
                title: 'Use File Explorer folder sharing',
                subtitle: 'Open the backup folder in File Explorer, then go to the Sharing options for that folder.',
                windowTitle: 'Windows File Explorer',
                menuTrail: ['Folder', 'Properties', 'Sharing', 'Advanced'],
                focusLabel: 'Share this folder',
                focusHint: 'After turning this on, give the other computers Read and Change permission so Enapel can save backups there.',
                shortcuts: ['Share', 'Permissions', 'UNC Path'],
            },
            steps: [
                {
                    title: 'Create the backup folder',
                    detail: 'Create a folder such as C:\EnapelBackups on the computer that will hold backups.',
                },
                {
                    title: 'Open folder sharing',
                    detail: 'Right-click the folder, choose Properties, then open the Sharing tab.',
                },
                {
                    title: 'Turn sharing on',
                    detail: 'Choose Advanced Sharing and tick Share this folder.',
                },
                {
                    title: 'Allow backup access',
                    detail: 'Open Permissions and allow the users or computers that need it to have Read and Change access.',
                },
                {
                    title: 'Use the shared path in Enapel',
                    detail: 'Paste \\COMPUTER-NAME\EnapelBackups into the Shared Backup Folder field in Enapel.',
                },
            ],
            tip: 'If you are not sure of the computer name, open Settings and search for “About your PC”.',
        },
        mac: {
            label: 'macOS',
            theme: 'violet',
            intro: 'macOS can share folders over the network using File Sharing.',
            path: '\\MAC-NAME\EnapelBackups',
            preview: {
                title: 'Use System Settings → Sharing',
                subtitle: 'Turn on File Sharing, then add your backup folder under Shared Folders.',
                windowTitle: 'macOS System Settings',
                menuTrail: ['General', 'Sharing', 'File Sharing', 'Options'],
                focusLabel: 'File Sharing',
                focusHint: 'Once File Sharing is enabled, add your Enapel backup folder and give the required users Read & Write access.',
                shortcuts: ['Finder', 'Sharing', 'Users'],
            },
            steps: [
                {
                    title: 'Create the backup folder',
                    detail: 'Create a folder such as EnapelBackups in Finder on the Mac that will store backups.',
                },
                {
                    title: 'Open sharing settings',
                    detail: 'Open System Settings, choose General, then choose Sharing.',
                },
                {
                    title: 'Turn on File Sharing',
                    detail: 'Switch on File Sharing and add your backup folder under Shared Folders.',
                },
                {
                    title: 'Allow write access',
                    detail: 'Set the required users to Read & Write so Enapel can save backups into the folder.',
                },
                {
                    title: 'Use the shared path in Enapel',
                    detail: 'Paste \\MAC-NAME\EnapelBackups into the Shared Backup Folder field in Enapel.',
                },
            ],
            tip: 'You can see the Mac name in System Settings → General → About or in the Sharing page itself.',
        },
        linux: {
            label: 'Linux',
            theme: 'emerald',
            intro: 'Linux usually shares folders using Samba.',
            path: '\\SERVER-NAME\EnapelBackups',
            preview: {
                title: 'Use Samba or your file manager sharing menu',
                subtitle: 'Many Linux desktops let you right-click the folder and turn on Local Network Share or Samba sharing.',
                windowTitle: 'Linux Folder Sharing',
                menuTrail: ['Folder', 'Share', 'Samba', 'Permissions'],
                focusLabel: 'Local Network Share',
                focusHint: 'If your desktop does not show this option, your Linux administrator may need to enable Samba first.',
                shortcuts: ['Folder', 'Samba', 'Write'],
            },
            steps: [
                {
                    title: 'Create the backup folder',
                    detail: 'Create a folder named EnapelBackups or another folder you want Enapel to use for backups.',
                },
                {
                    title: 'Enable sharing',
                    detail: 'Use your file manager sharing menu or Samba settings to share that folder on the network.',
                },
                {
                    title: 'Allow read and write access',
                    detail: 'Make sure the required users or devices can read and write to the shared folder.',
                },
                {
                    title: 'Ask for Samba help if needed',
                    detail: 'If there is no sharing option yet, ask your Linux administrator to install or enable Samba file sharing.',
                },
                {
                    title: 'Use the shared path in Enapel',
                    detail: 'Paste \\SERVER-NAME\EnapelBackups into the Shared Backup Folder field in Enapel.',
                },
            ],
            tip: 'Linux menu names vary the most, but you usually need Samba or a “Local Network Share” option.',
        },
    };

    const guide = osGuides[detectedOs] || osGuides.windows;

    React.useEffect(() => {
        if (!show) {
            setCopiedExamplePath(false);

            if (copyTimeoutRef.current) {
                window.clearTimeout(copyTimeoutRef.current);
                copyTimeoutRef.current = null;
            }
        }
    }, [show]);

    React.useEffect(() => {
        return () => {
            if (copyTimeoutRef.current) {
                window.clearTimeout(copyTimeoutRef.current);
            }
        };
    }, []);

    const copyExamplePath = async () => {
        try {
            if (!window.navigator?.clipboard) {
                throw new Error('Clipboard is not available');
            }

            await window.navigator.clipboard.writeText(guide.path);
            setCopiedExamplePath(true);

            if (copyTimeoutRef.current) {
                window.clearTimeout(copyTimeoutRef.current);
            }

            copyTimeoutRef.current = window.setTimeout(() => {
                setCopiedExamplePath(false);
                copyTimeoutRef.current = null;
            }, 2000);
        } catch (error) {
            window.alert('Could not copy the example path.');
        }
    };

    if (!show) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div className="bg-white rounded-[2rem] p-6 max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="text-xs font-bold uppercase tracking-[0.25em] text-blue-600">Folder Sharing Tutorial</p>
                        <h3 className="text-2xl font-black text-gray-900 mt-2">How to share a folder on {guide.label}</h3>
                        <p className="text-gray-500 font-medium mt-2">{guide.intro}</p>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="h-10 w-10 rounded-2xl bg-gray-100 text-gray-700 font-black"
                    >
                        ×
                    </button>
                </div>

                <div className="mt-6 grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
                    <SharingPreviewCard guide={guide} detectedOs={detectedOs} />

                    <div className="rounded-3xl border border-blue-200 bg-blue-50 px-4 py-4">
                        <p className="text-sm font-black text-blue-950">Shared path example</p>
                        <p className="mt-2 font-mono text-sm text-blue-900 break-all">{guide.path}</p>
                        <p className="mt-3 text-sm text-blue-900 leading-6">
                            After you share the folder, this is the kind of network path you should paste into the Shared Backup Folder field in Enapel.
                        </p>
                        <button
                            type="button"
                            onClick={copyExamplePath}
                            className="mt-4 inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-blue-500/20 transition hover:bg-blue-700"
                        >
                            {copiedExamplePath ? 'Example Path Copied' : 'Copy Example Path'}
                        </button>
                    </div>
                </div>

                <div className="mt-6 space-y-3">
                    {guide.steps.map((step, index) => (
                        <div key={step.title} className="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                            <div className="flex items-start gap-3">
                                <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-2xl bg-gray-900 text-xs font-black text-white">
                                    {index + 1}
                                </div>
                                <div>
                                    <p className="text-sm font-black text-gray-900">{step.title}</p>
                                    <p className="mt-1 text-sm text-gray-700 leading-6">{step.detail}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-950">
                    <p className="font-black">Helpful tip</p>
                    <p className="mt-2 leading-6">{guide.tip}</p>
                </div>

                <div className="mt-6 rounded-3xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-900">
                    Menu names can vary a little by Windows, macOS, or Linux version. If your screen looks slightly different, look for the same sharing options.
                </div>
            </div>
        </div>
    );
}

export default function DisasterRecovery({
    settings,
    nodeState,
    healthWarnings,
    latestBackups,
    replicationNodes,
    availableNasBundles,
    networkAddresses = [],
    serverPort = 8000,
    isNativeDesktop = false,
}) {
    const { flash } = usePage().props;
    const [copiedSetupCode, setCopiedSetupCode] = React.useState(false);
    const [creatingSetupCode, setCreatingSetupCode] = React.useState(false);
    const [sharingHelpOpen, setSharingHelpOpen] = React.useState(false);
    const settingsForm = useForm({
        node_name: settings?.node_name || nodeState?.node_name || '',
        node_role: settings?.node_role || nodeState?.role || 'primary',
        service_hostname: settings?.service_hostname || '',
        nas_path: settings?.nas_path || '',
        cloud_mirror_enabled: Boolean(settings?.cloud_mirror_enabled),
        cloud_mirror_url: settings?.cloud_mirror_url || '',
        cloud_mirror_token: '',
        snapshot_interval_minutes: settings?.snapshot_interval_minutes || 15,
        full_backup_hour: settings?.full_backup_hour || 2,
        monthly_backup_hour: settings?.monthly_backup_hour || 3,
        retention_snapshot_days: settings?.retention_snapshot_days || 7,
        retention_daily_backups: settings?.retention_daily_backups || 30,
        retention_monthly_backups: settings?.retention_monthly_backups || 12,
        standby_enabled: Boolean(settings?.standby_enabled),
        standby_primary_url: settings?.standby_primary_url || '',
        passphrase: '',
        passphrase_confirmation: '',
        passphrase_hint: settings?.passphrase_hint || '',
    });

    const pairForm = useForm({
        primary_url: settings?.standby_primary_url || '',
        pairing_token: '',
    });

    const promoteForm = useForm({
        passphrase: '',
    });

    const isMainServer = settingsForm.data.node_role === 'primary';
    const currentRoleLabel = isMainServer ? 'Main Server' : 'Backup Server';
    const networkUrls = networkAddresses.map((address) => `http://${address}:${serverPort}`);
    const [showPassword, setShowPassword] = React.useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = React.useState(false);
    const shouldShowConfirm = settingsForm.data.passphrase.length > 0;
    const hasSavedBackupPassword = Boolean(settings?.has_backup_password);
    const detectedOs = React.useMemo(() => detectOperatingSystem(), []);

    const validateSettingsForm = () => {
        const errors = {};

        if (!settingsForm.data.nas_path?.trim()) {
            errors.nas_path = 'Choose the shared backup folder.';
        }

        const enteringPassword = settingsForm.data.passphrase.length > 0;
        const needsPassword = !hasSavedBackupPassword || enteringPassword;

        if (needsPassword && !settingsForm.data.passphrase.trim()) {
            errors.passphrase = 'Enter a backup password.';
        } else if (enteringPassword && settingsForm.data.passphrase.length < 12) {
            errors.passphrase = 'Backup password must be at least 12 characters.';
        }

        if (needsPassword && settingsForm.data.passphrase !== settingsForm.data.passphrase_confirmation) {
            errors.passphrase_confirmation = 'Backup password confirmation does not match.';
        }

        if (!isMainServer && !settingsForm.data.standby_primary_url?.trim()) {
            errors.standby_primary_url = 'Enter the main server address.';
        }

        if (isMainServer && settingsForm.data.cloud_mirror_enabled && !settingsForm.data.cloud_mirror_url?.trim()) {
            errors.cloud_mirror_url = 'Enter the online backup address or switch off the online copy option.';
        }

        settingsForm.clearErrors();

        if (Object.keys(errors).length > 0) {
            settingsForm.setError(errors);
            return false;
        }

        return true;
    };

    const saveSettings = (e) => {
        e.preventDefault();

        if (!validateSettingsForm()) {
            return;
        }

        settingsForm.transform((data) => ({
            ...data,
            cloud_mirror_enabled: isMainServer && data.cloud_mirror_enabled ? 1 : 0,
            standby_enabled: isMainServer ? 0 : 1,
            standby_primary_url: isMainServer ? '' : data.standby_primary_url,
        })).put(route('global.settings.disaster-recovery.update'), {
            preserveScroll: true,
        });
    };

    const runSnapshot = (type, full = false) => {
        router.post(route('global.settings.disaster-recovery.snapshot'), { type, full });
    };

    const pickFolder = async () => {
        try {
            const response = await window.axios.post(route('global.settings.disaster-recovery.pick-folder'), {
                current: settingsForm.data.nas_path,
            });

            if (response.data?.path) {
                settingsForm.setData('nas_path', response.data.path);
            }
        } catch (error) {
            const message = error?.response?.data?.message || 'Could not open the folder chooser.';
            window.alert(message);
        }
    };

    const createSetupCode = () => {
        router.post(route('global.settings.disaster-recovery.pairing-token'), {}, {
            preserveScroll: true,
            onStart: () => setCreatingSetupCode(true),
            onFinish: () => setCreatingSetupCode(false),
        });
    };

    const copySetupCode = async () => {
        if (!flash?.pairing_token) {
            return;
        }

        try {
            await navigator.clipboard.writeText(flash.pairing_token);
            setCopiedSetupCode(true);
            window.setTimeout(() => setCopiedSetupCode(false), 2000);
        } catch (error) {
            window.alert('Could not copy the setup code.');
        }
    };

    return (
        <div className="py-8 px-4 sm:px-6 lg:px-8  space-y-8">
            <Head title="Backup Settings" />
            <SharingHelpModal
                show={sharingHelpOpen}
                onClose={() => setSharingHelpOpen(false)}
                detectedOs={detectedOs}
            />

            <div className="flex flex-col xl:flex-row xl:items-end justify-between gap-4">
                <div>
                    <p className="text-xs font-bold uppercase tracking-[0.3em] text-blue-600">Global Settings</p>
                    <h1 className="text-4xl font-black text-gray-900 tracking-tight mt-2">Backup Settings</h1>
                    <p className="text-gray-500 font-medium mt-2 max-w-3xl">
                        If you only want backups, install Enapel on one computer. If you also want a second backup computer ready to take over, install Enapel on that second computer too.
                    </p>
                </div>

                <div className="flex flex-wrap items-center gap-3">
                    {isMainServer && (
                        <>
                            <button
                                onClick={() => runSnapshot('snapshot', false)}
                                className="px-5 py-3 rounded-2xl bg-blue-600 text-white font-bold shadow-lg shadow-blue-500/20"
                            >
                                Save Backup Now
                            </button>
                            <button
                                onClick={() => runSnapshot('daily', true)}
                                className="px-5 py-3 rounded-2xl bg-gray-900 text-white font-bold"
                            >
                                Save Full Backup Now
                            </button>
                        </>
                    )}
                    <Link
                        href={route('disaster-recovery.restore.create')}
                        className="px-5 py-3 rounded-2xl bg-white border border-gray-200 text-gray-900 font-bold"
                    >
                        Restore From Backup
                    </Link>
                </div>
            </div>

            {(flash?.success || flash?.pairing_token) && (
                <div className="grid lg:grid-cols-3 gap-4">
                    {flash?.success && (
                        <div className="lg:col-span-2 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-semibold">
                            {flash.success}
                        </div>
                    )}
                </div>
            )}

            {Object.keys(settingsForm.errors).length > 0 && (
                <div className="rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800">
                    <p className="font-black">Please correct the highlighted fields.</p>
                </div>
            )}

            <SectionCard
                title="Choose How This Computer Should Work"
                subtitle="Main Server = the live Enapel computer. Backup Server = the second computer kept ready for emergencies."
            >
                <div className="grid md:grid-cols-2 gap-4">
                    <button
                        type="button"
                        onClick={() => settingsForm.setData('node_role', 'primary')}
                        className={`text-left rounded-[2rem] border p-5 transition-colors ${isMainServer ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'}`}
                    >
                        <p className="text-lg font-black text-gray-900">Main Server</p>
                        <p className="text-sm text-gray-600 mt-2">Use this on the computer already running Enapel for users right now.</p>
                    </button>
                    <button
                        type="button"
                        onClick={() => settingsForm.setData('node_role', 'standby')}
                        className={`text-left rounded-[2rem] border p-5 transition-colors ${!isMainServer ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'}`}
                    >
                        <p className="text-lg font-black text-gray-900">Backup Server</p>
                        <p className="text-sm text-gray-600 mt-2">Use this only on a second computer that should take over if the main server fails.</p>
                    </button>
                </div>
            </SectionCard>

            <div className="grid xl:grid-cols-[1.35fr_0.85fr] gap-6">
                <form onSubmit={saveSettings} className="space-y-6">
                    <SectionCard
                        title={`${currentRoleLabel} Setup`}
                        subtitle={isMainServer
                            ? 'These are the only fields needed for the live Enapel computer.'
                            : 'These are the only fields needed for the second backup computer.'}
                    >
                        <label className="space-y-2 block">
                            <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Shared Backup Folder</span>
                            <div className="flex flex-col sm:flex-row gap-3">
                                <input
                                    className={inputClass(Boolean(settingsForm.errors.nas_path), 'flex-1 font-mono text-sm')}
                                    placeholder="\\\\Office-Storage\\Enapel\\Backups"
                                    value={settingsForm.data.nas_path}
                                    onChange={(e) => settingsForm.setData('nas_path', e.target.value)}
                                />
                                {isNativeDesktop && (
                                    <button
                                        type="button"
                                        onClick={pickFolder}
                                        className="px-4 py-3 rounded-2xl bg-gray-900 text-white font-bold whitespace-nowrap"
                                    >
                                        Choose Folder
                                    </button>
                                )}
                            </div>
                            <HelpText>
                                This is where backup files are saved. Choose an already shared office folder. The app will not create a Windows network share automatically.
                            </HelpText>
                            <button
                                type="button"
                                onClick={() => setSharingHelpOpen(true)}
                                className="text-sm font-bold text-blue-600 hover:text-blue-700 text-left"
                            >
                                How do I share a folder on this {detectedOs === 'mac' ? 'Mac' : detectedOs === 'linux' ? 'Linux computer' : 'Windows computer'}?
                            </button>
                            {settingsForm.errors.nas_path && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.nas_path}</p>}
                        </label>

                        <label className="space-y-2 block">
                            <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Backup Password</span>
                            <div className="relative">
                                <input
                                    type={showPassword ? 'text' : 'password'}
                                    className={inputClass(Boolean(settingsForm.errors.passphrase), 'pr-14')}
                                    value={settingsForm.data.passphrase}
                                    onChange={(e) => settingsForm.setData('passphrase', e.target.value)}
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword((value) => !value)}
                                    className="absolute inset-y-0 right-3 my-auto h-9 px-2 rounded-xl text-gray-500 hover:text-gray-900"
                                >
                                    {showPassword ? 'Hide' : 'Show'}
                                </button>
                            </div>
                            <HelpText>Use the same backup password on the main server and the backup server. This password locks and unlocks the backup files.</HelpText>
                            {settingsForm.errors.passphrase && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.passphrase}</p>}
                        </label>

                        {shouldShowConfirm && (
                            <label className="space-y-2 block">
                                <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Confirm Backup Password</span>
                                <div className="relative">
                                    <input
                                        type={showConfirmPassword ? 'text' : 'password'}
                                        className={inputClass(Boolean(settingsForm.errors.passphrase_confirmation), 'pr-14')}
                                        value={settingsForm.data.passphrase_confirmation}
                                        onChange={(e) => settingsForm.setData('passphrase_confirmation', e.target.value)}
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowConfirmPassword((value) => !value)}
                                        className="absolute inset-y-0 right-3 my-auto h-9 px-2 rounded-xl text-gray-500 hover:text-gray-900"
                                    >
                                        {showConfirmPassword ? 'Hide' : 'Show'}
                                    </button>
                                </div>
                                <HelpText>Only needed when you are setting or changing the backup password.</HelpText>
                                {settingsForm.errors.passphrase_confirmation && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.passphrase_confirmation}</p>}
                            </label>
                        )}

                        {isMainServer ? (
                            <>
                                <div className="rounded-[2rem] border border-blue-200 bg-blue-50 p-5">
                                    <p className="text-lg font-black text-blue-950">How other computers will reach this server</p>
                                    <p className="text-sm text-blue-900 mt-2">
                                        Enapel already uses port <span className="font-black">{serverPort}</span> automatically.
                                    </p>
                                    {networkUrls.length > 0 ? (
                                        <div className="mt-4 space-y-2">
                                            {networkUrls.map((url) => (
                                                <div key={url} className="rounded-2xl bg-white px-4 py-3 font-mono text-sm text-blue-950 border border-blue-100">
                                                    {url}
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <p className="text-sm text-blue-900 mt-3">
                                            No network address was detected automatically on this computer yet.
                                        </p>
                                    )}
                                </div>

                                <div className="grid md:grid-cols-3 gap-5">
                                    <label className="space-y-2">
                                        <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Save Backup Every</span>
                                        <input
                                            type="number"
                                            min="5"
                                            className={inputClass(Boolean(settingsForm.errors.snapshot_interval_minutes))}
                                            value={settingsForm.data.snapshot_interval_minutes}
                                            onChange={(e) => settingsForm.setData('snapshot_interval_minutes', e.target.value)}
                                        />
                                        <HelpText>Use `15` for every 15 minutes.</HelpText>
                                        {settingsForm.errors.snapshot_interval_minutes && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.snapshot_interval_minutes}</p>}
                                    </label>

                                    <label className="space-y-2">
                                        <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Full Backup Each Night</span>
                                        <input
                                            type="number"
                                            min="0"
                                            max="23"
                                            className={inputClass(Boolean(settingsForm.errors.full_backup_hour))}
                                            value={settingsForm.data.full_backup_hour}
                                            onChange={(e) => settingsForm.setData('full_backup_hour', e.target.value)}
                                        />
                                        <HelpText>Use `2` for 2 AM.</HelpText>
                                        {settingsForm.errors.full_backup_hour && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.full_backup_hour}</p>}
                                    </label>

                                    <label className="space-y-2">
                                        <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Monthly Full Backup</span>
                                        <input
                                            type="number"
                                            min="0"
                                            max="23"
                                            className={inputClass(Boolean(settingsForm.errors.monthly_backup_hour))}
                                            value={settingsForm.data.monthly_backup_hour}
                                            onChange={(e) => settingsForm.setData('monthly_backup_hour', e.target.value)}
                                        />
                                        <HelpText>Use `3` for 3 AM on the first day of the month.</HelpText>
                                        {settingsForm.errors.monthly_backup_hour && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.monthly_backup_hour}</p>}
                                    </label>
                                </div>

                                <div className="rounded-3xl border border-gray-200 bg-gray-50 p-5 space-y-4">
                                    <div className="flex items-center justify-between gap-4">
                                        <div>
                                            <p className="font-black text-gray-900">Also save a copy online</p>
                                            <p className="text-sm text-gray-500 mt-1">Optional. Ignore this if you only want the shared office backup folder.</p>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => settingsForm.setData('cloud_mirror_enabled', !settingsForm.data.cloud_mirror_enabled)}
                                            className={`relative w-12 h-7 rounded-full ${settingsForm.data.cloud_mirror_enabled ? 'bg-blue-600' : 'bg-gray-300'}`}
                                        >
                                            <span className={`absolute top-1 left-1 h-5 w-5 rounded-full bg-white transition-transform ${settingsForm.data.cloud_mirror_enabled ? 'translate-x-5' : ''}`} />
                                        </button>
                                    </div>
                                    <input
                                        className={inputClass(Boolean(settingsForm.errors.cloud_mirror_url))}
                                        placeholder="https://backup.example.com/api/upload"
                                        value={settingsForm.data.cloud_mirror_url}
                                        onChange={(e) => settingsForm.setData('cloud_mirror_url', e.target.value)}
                                    />
                                    {settingsForm.errors.cloud_mirror_url && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.cloud_mirror_url}</p>}
                                    <input
                                        className={inputClass(Boolean(settingsForm.errors.cloud_mirror_token))}
                                        placeholder="Optional online backup token"
                                        value={settingsForm.data.cloud_mirror_token}
                                        onChange={(e) => settingsForm.setData('cloud_mirror_token', e.target.value)}
                                    />
                                    {settingsForm.errors.cloud_mirror_token && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.cloud_mirror_token}</p>}
                                </div>
                            </>
                        ) : (
                            <>
                                <label className="space-y-2 block">
                                    <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Main Server Address</span>
                                    <input
                                        className={inputClass(Boolean(settingsForm.errors.standby_primary_url))}
                                        placeholder="http://192.168.1.20:8000"
                                        value={settingsForm.data.standby_primary_url}
                                        onChange={(e) => {
                                            settingsForm.setData('standby_primary_url', e.target.value);
                                            pairForm.setData('primary_url', e.target.value);
                                        }}
                                    />
                                    <HelpText>Enter the address shown on the main server. Port {serverPort} is used automatically.</HelpText>
                                    {settingsForm.errors.standby_primary_url && <p className="text-sm font-semibold text-rose-600">{settingsForm.errors.standby_primary_url}</p>}
                                </label>
                            </>
                        )}

                        <div className="flex justify-end">
                            <button type="submit" disabled={settingsForm.processing} className="px-6 py-3 rounded-2xl bg-gray-900 text-white font-black disabled:opacity-60">
                                {settingsForm.processing ? 'Saving...' : 'Save Settings'}
                            </button>
                        </div>
                    </SectionCard>
                </form>

                <div className="space-y-6">
                    <SectionCard title="Setup Check" subtitle="This tells you if something important is still missing.">
                        {healthWarnings?.length ? (
                            <div className="space-y-3">
                                {healthWarnings.map((warning) => (
                                    <div key={warning} className="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">
                                        {warning}
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                                This setup looks okay.
                            </div>
                        )}

                        <div className="grid grid-cols-2 gap-3">
                            <div className="rounded-2xl bg-gray-50 p-4">
                                <p className="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Last Small Backup</p>
                                <p className="mt-2 text-sm font-black text-gray-900">{formatAgo(settings?.last_successful_snapshot_at)}</p>
                            </div>
                            <div className="rounded-2xl bg-gray-50 p-4">
                                <p className="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Last Full Backup</p>
                                <p className="mt-2 text-sm font-black text-gray-900">{formatAgo(settings?.last_successful_full_backup_at)}</p>
                            </div>
                        </div>
                    </SectionCard>

                    {isMainServer ? (
                        <SectionCard title="If You Want A Backup Server" subtitle="Use this only when you have a second computer with Enapel installed on it.">
                            <div className="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 text-sm text-blue-950 space-y-2">
                                <p>1. Save this page on the main server.</p>
                                <p>2. Click `Create Setup Code` below.</p>
                                <p>3. Open Enapel on the second computer, choose `Backup Server`, then enter the setup code and the main server address.</p>
                            </div>

                            <button
                                onClick={createSetupCode}
                                disabled={creatingSetupCode}
                                className="w-full px-4 py-3 rounded-2xl bg-blue-600 text-white font-bold disabled:opacity-60"
                            >
                                {creatingSetupCode ? 'Creating Setup Code...' : 'Create Setup Code'}
                            </button>

                            {flash?.pairing_token && (
                                <div className="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 space-y-3">
                                    <div className="flex items-center justify-between gap-3">
                                        <p className="text-sm font-black text-blue-950">Setup Code</p>
                                        <button
                                            type="button"
                                            onClick={copySetupCode}
                                            className="px-3 py-1.5 rounded-xl bg-white border border-blue-200 text-blue-900 text-sm font-bold"
                                        >
                                            {copiedSetupCode ? 'Copied' : 'Copy'}
                                        </button>
                                    </div>
                                    <p className="font-mono text-sm text-blue-900 break-all">{flash.pairing_token}</p>
                                    <p className="text-sm text-blue-900">
                                        This is not the same as the backup password. It is a one-time code used only to connect the backup server to the main server.
                                    </p>
                                </div>
                            )}

                            {replicationNodes?.length > 0 && (
                                <div className="space-y-3">
                                    {replicationNodes.map((node) => (
                                        <div key={node.id} className="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                                            <p className="font-black text-gray-900">{node.name}</p>
                                            <p className="text-sm text-gray-500 mt-1">{node.base_url || node.hostname || 'No address shared yet'}</p>
                                            <p className="text-sm font-semibold text-gray-700 mt-3">
                                                Status: {node.status} {node.sync_lag_seconds !== null ? `| Delay: ${node.sync_lag_seconds}s` : ''}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </SectionCard>
                    ) : (
                        <SectionCard title="Connect This Backup Server" subtitle="Use the setup code created on the main server.">
                            <form
                                onSubmit={(e) => {
                                    e.preventDefault();
                                    pairForm.post(route('global.settings.disaster-recovery.pair'), {
                                        preserveScroll: true,
                                    });
                                }}
                                className="space-y-4"
                            >
                                <label className="space-y-2 block">
                                    <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Main Server Address</span>
                                    <input
                                        className="w-full rounded-2xl border-gray-200"
                                        placeholder="http://192.168.1.20:8000"
                                        value={pairForm.data.primary_url}
                                        onChange={(e) => pairForm.setData('primary_url', e.target.value)}
                                    />
                                    {pairForm.errors.primary_url && <p className="text-sm font-semibold text-rose-600">{pairForm.errors.primary_url}</p>}
                                </label>

                                <label className="space-y-2 block">
                                    <span className="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Setup Code From Main Server</span>
                                    <input
                                        className="w-full rounded-2xl border-gray-200 font-mono text-sm"
                                        value={pairForm.data.pairing_token}
                                        onChange={(e) => pairForm.setData('pairing_token', e.target.value)}
                                    />
                                    {pairForm.errors.pairing_token && <p className="text-sm font-semibold text-rose-600">{pairForm.errors.pairing_token}</p>}
                                </label>

                                <button type="submit" disabled={pairForm.processing} className="w-full px-4 py-3 rounded-2xl bg-gray-900 text-white font-bold disabled:opacity-60">
                                    {pairForm.processing ? 'Connecting...' : 'Connect Backup Server'}
                                </button>
                            </form>

                            <div className="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-950 space-y-2">
                                <p>If the main server fails, open this backup server locally and click the button below.</p>
                            </div>

                            <form
                                onSubmit={(e) => {
                                    e.preventDefault();
                                    promoteForm.post(route('global.settings.disaster-recovery.promote'), {
                                        preserveScroll: true,
                                    });
                                }}
                                className="space-y-3"
                            >
                                <input
                                    type="password"
                                    className="w-full rounded-2xl border-gray-200"
                                    placeholder="Optional backup password"
                                    value={promoteForm.data.passphrase}
                                    onChange={(e) => promoteForm.setData('passphrase', e.target.value)}
                                />
                                <button type="submit" disabled={promoteForm.processing} className="w-full px-4 py-3 rounded-2xl bg-rose-600 text-white font-bold disabled:opacity-60">
                                    {promoteForm.processing ? 'Switching...' : 'Make Backup Server Live'}
                                </button>
                            </form>
                        </SectionCard>
                    )}
                </div>
            </div>

            <details className="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-xl shadow-gray-200/40">
                <summary className="cursor-pointer text-2xl font-black text-gray-900">Show Backup Lists</summary>
                <div className="grid xl:grid-cols-2 gap-6 mt-6">
                    <div className="space-y-3">
                        <h3 className="text-lg font-black text-gray-900">Recent Saved Backups</h3>
                        {latestBackups?.length ? latestBackups.map((backup) => (
                            <div key={backup.id} className="rounded-3xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <div className="flex items-center justify-between gap-3">
                                    <p className="font-black text-gray-900">{backup.type}</p>
                                    <p className="text-sm font-semibold text-gray-700">{backup.status}</p>
                                </div>
                                <p className="text-xs font-mono text-gray-500 mt-2 break-all">{backup.bundle_uuid}</p>
                                <p className="text-sm text-gray-600 mt-2">{formatAgo(backup.completed_at || backup.started_at)}</p>
                            </div>
                        )) : (
                            <TablePlaceholder 
                                title="No backups recorded"
                                description="The local activity log does not show any recent backup operations. Start a backup to see entries here."
                                icon="📦"
                            />
                        )}
                    </div>

                    <div className="space-y-3">
                        <h3 className="text-lg font-black text-gray-900">Backup Files Found In Shared Folder</h3>
                        {availableNasBundles?.length ? availableNasBundles.map((bundle) => (
                            <div key={bundle.path} className="rounded-3xl border border-gray-100 bg-gray-50 px-5 py-4">
                                <p className="font-black text-gray-900">{bundle.header?.type || 'backup'} {bundle.header?.full ? 'full' : 'small'}</p>
                                <p className="text-xs font-mono text-gray-500 mt-2 break-all">{bundle.header?.bundle_uuid}</p>
                                <p className="text-sm text-gray-600 mt-2">{bundle.path}</p>
                            </div>
                        )) : (
                            <TablePlaceholder 
                                title="No backup files found"
                                description="We couldn't detect any valid Enapel backup bundles in the configured shared folder. Ensure the path is correct and accessible."
                                icon="🔍"
                            />
                        )}
                    </div>
                </div>
            </details>
        </div>
    );
}

DisasterRecovery.layout = (page) => <AuthenticatedLayout>{page}</AuthenticatedLayout>;
