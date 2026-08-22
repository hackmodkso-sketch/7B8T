<?php
// Strict session configuration for forced login
ini_set('session.gc_maxlifetime', 3600);
session_start();

// --- SET YOUR SECURE PASSWORD HERE ---
$PASSWORD = "Abc658188"; 
$SETTINGS_FILE = 'settings.json';

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Handle Login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['logged_in'] = true;
        header("Location: admin.php"); // Refresh to prevent form resubmission
        exit;
    } else {
        $error = "Access Denied. Incorrect password.";
    }
}

// Handle Settings Update
if (isset($_POST['update_settings']) && isset($_SESSION['logged_in'])) {
    $newSettings = [
        "motd" => htmlspecialchars($_POST['motd'] ?? ""),
        "showJson" => isset($_POST['showJson']) ? true : false,
        "pingIp" => htmlspecialchars($_POST['pingIp'] ?? ""),
        "pingPort" => htmlspecialchars($_POST['pingPort'] ?? ""),
        "displayIp" => htmlspecialchars($_POST['displayIp'] ?? ""),
        "displayPort" => htmlspecialchars($_POST['displayPort'] ?? ""),
        "themeMode" => htmlspecialchars($_POST['themeMode'] ?? "rainbow"),
        "themeColor" => htmlspecialchars($_POST['themeColor'] ?? "#f59e0b"),
        "rainbowSpeed" => (int)($_POST['rainbowSpeed'] ?? 8000),
        "pingDelay" => (int)($_POST['pingDelay'] ?? 30000),
        "discordLink" => htmlspecialchars($_POST['discordLink'] ?? ""),
        "rulesText" => htmlspecialchars($_POST['rulesText'] ?? ""),
        "maintenanceMode" => isset($_POST['maintenanceMode']) ? true : false
    ];
    file_put_contents($SETTINGS_FILE, json_encode($newSettings, JSON_PRETTY_PRINT));
    $success = "System Configuration Saved Successfully!";
}

// Load current settings
$currentSettings = json_decode(file_get_contents($SETTINGS_FILE), true);
if (!$currentSettings) {
    // Failsafe defaults if JSON is corrupted
    $currentSettings = ["themeMode" => "rainbow", "themeColor" => "#f59e0b", "rainbowSpeed" => 8000, "pingDelay" => 30000, "maintenanceMode" => false];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>7B8T - Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #020617; color: #f8fafc; font-family: system-ui, sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 1rem; }
        input[type="color"] { -webkit-appearance: none; border: none; padding: 0; }
        input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
        input[type="color"]::-webkit-color-swatch { border: none; border-radius: 4px; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-10 flex flex-col items-center justify-center bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]">

    <?php if (!isset($_SESSION['logged_in'])): ?>
        <div class="glass w-full max-w-sm p-8 shadow-2xl transform transition hover:scale-105 duration-500">
            <h1 class="text-3xl font-black mb-2 text-yellow-500 uppercase text-center tracking-widest">7B8T</h1>
            <p class="text-center text-slate-400 text-xs tracking-[0.2em] mb-8">SECURE LOGIN</p>
            
            <?php if(isset($error)) echo "<p class='text-red-500 mb-4 text-center font-bold text-sm bg-red-500/10 p-2 rounded'>$error</p>"; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <input type="password" name="password" placeholder="Enter Password" required class="w-full bg-black/50 border border-slate-700 rounded-lg p-3 text-white focus:outline-none focus:border-yellow-500 text-center tracking-widest">
                </div>
                <button type="submit" class="w-full bg-yellow-500 text-black font-black py-3 rounded-lg uppercase tracking-widest hover:bg-yellow-400 transition shadow-[0_0_15px_rgba(245,158,11,0.4)]">Authenticate</button>
            </form>
        </div>

    <?php else: ?>
        <div class="w-full max-w-4xl">
            <div class="flex justify-between items-end mb-6 px-2">
                <div>
                    <h1 class="text-4xl font-black text-yellow-500 uppercase tracking-widest drop-shadow-lg">Command Center</h1>
                    <p class="text-slate-400 text-sm tracking-[0.1em]">Live Server Configuration</p>
                </div>
                <a href="?logout=true" class="bg-red-500/20 text-red-500 border border-red-500/50 font-bold py-2 px-6 rounded-lg uppercase text-sm hover:bg-red-500 hover:text-white transition">Logout</a>
            </div>

            <?php if(isset($success)) echo "<div class='bg-green-500/20 border border-green-500 text-green-400 p-4 rounded-lg mb-6 text-center font-bold tracking-wider'>$success</div>"; ?>
            
            <form method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="glass p-6 space-y-4">
                        <h2 class="text-yellow-500 font-bold uppercase tracking-widest border-b border-white/10 pb-2 mb-4 text-sm">Server Connection</h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Display IP</label>
                                <input type="text" name="displayIp" value="<?= $currentSettings['displayIp'] ?>" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Display Port</label>
                                <input type="text" name="displayPort" value="<?= $currentSettings['displayPort'] ?>" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Ping Target IP</label>
                                <input type="text" name="pingIp" value="<?= $currentSettings['pingIp'] ?>" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm border-blue-500/50">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Ping Target Port</label>
                                <input type="text" name="pingPort" value="<?= $currentSettings['pingPort'] ?>" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm border-blue-500/50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Ping Refresh Delay (ms)</label>
                            <input type="number" name="pingDelay" value="<?= $currentSettings['pingDelay'] ?>" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm">
                            <p class="text-[9px] text-slate-500 mt-1">30000 = 30 seconds</p>
                        </div>
                    </div>

                    <div class="glass p-6 space-y-4">
                        <h2 class="text-yellow-500 font-bold uppercase tracking-widest border-b border-white/10 pb-2 mb-4 text-sm">Appearance & Theme</h2>
                        
                        <div>
                            <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Theme Mode</label>
                            <select name="themeMode" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm">
                                <option value="rainbow" <?= $currentSettings['themeMode'] == 'rainbow' ? 'selected' : '' ?>>Rainbow Animated</option>
                                <option value="static" <?= $currentSettings['themeMode'] == 'static' ? 'selected' : '' ?>>Static Color</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Static Color</label>
                                <input type="color" name="themeColor" value="<?= $currentSettings['themeColor'] ?>" class="w-full h-[38px] bg-black/50 border border-slate-700 rounded cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Rainbow Speed (ms)</label>
                                <input type="number" name="rainbowSpeed" value="<?= $currentSettings['rainbowSpeed'] ?>" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass p-6 space-y-4">
                    <h2 class="text-yellow-500 font-bold uppercase tracking-widest border-b border-white/10 pb-2 mb-4 text-sm">Content & Links</h2>
                    
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Main Description / MOTD</label>
                        <input type="text" name="motd" value="<?= $currentSettings['motd'] ?>" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Discord Invite Link</label>
                        <input type="text" name="discordLink" value="<?= $currentSettings['discordLink'] ?>" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm text-blue-400">
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1 uppercase tracking-wider">Server Rules (One rule per line)</label>
                        <textarea name="rulesText" rows="4" class="w-full bg-black/50 border border-slate-700 rounded p-2 text-white text-sm leading-relaxed"><?= $currentSettings['rulesText'] ?></textarea>
                    </div>
                </div>

                <div class="glass p-6 grid grid-cols-1 md:grid-cols-2 gap-4 border border-slate-700">
                    <label class="flex items-center gap-3 p-4 bg-black/40 rounded border border-white/5 cursor-pointer hover:bg-black/60 transition">
                        <input type="checkbox" name="showJson" <?= $currentSettings['showJson'] ? 'checked' : '' ?> class="w-5 h-5 accent-blue-500">
                        <div>
                            <p class="text-sm font-bold text-white uppercase tracking-wider">Show Live API Panel</p>
                            <p class="text-[10px] text-slate-400">Displays the raw JSON block on the index.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 bg-red-900/20 rounded border border-red-500/30 cursor-pointer hover:bg-red-900/40 transition">
                        <input type="checkbox" name="maintenanceMode" <?= $currentSettings['maintenanceMode'] ? 'checked' : '' ?> class="w-5 h-5 accent-red-500">
                        <div>
                            <p class="text-sm font-bold text-red-400 uppercase tracking-wider">Maintenance Mode</p>
                            <p class="text-[10px] text-slate-400">Hides the site and shows "Fixing/Maintenance".</p>
                        </div>
                    </label>
                </div>

                <button type="submit" name="update_settings" class="w-full bg-yellow-500 text-black font-black py-4 rounded-xl uppercase tracking-[0.2em] hover:bg-yellow-400 transition shadow-[0_0_20px_rgba(245,158,11,0.3)]">Save Global Configuration</button>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>