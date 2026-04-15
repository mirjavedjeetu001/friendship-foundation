{{-- App Update Popup - Shows when user has an outdated Android app --}}
@php $minAppVersion = 9; @endphp
<div id="app-update-overlay" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,0.7); display:none; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#1e293b; border-radius:1rem; padding:2rem; max-width:360px; width:100%; text-align:center; box-shadow:0 25px 50px rgba(0,0,0,0.5);">
        <div style="width:64px; height:64px; margin:0 auto 1rem; background:linear-gradient(135deg,#f59e0b,#ef4444); border-radius:50%; display:flex; align-items:center; justify-content:center;">
            <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        </div>
        <h3 style="color:#f1f5f9; font-size:1.25rem; font-weight:700; margin-bottom:0.5rem;">আপডেট করুন!</h3>
        <p style="color:#94a3b8; font-size:0.875rem; line-height:1.5; margin-bottom:1.5rem;">
            Allied Group অ্যাপের নতুন ভার্সন এসেছে। ভালো অভিজ্ঞতার জন্য অনুগ্রহ করে আপডেট করুন।
        </p>
        <a href="{{ route('app.download') }}" 
           style="display:block; background:linear-gradient(135deg,#6366f1,#4f46e5); color:white; padding:0.75rem 1.5rem; border-radius:0.75rem; font-weight:600; font-size:0.95rem; text-decoration:none; margin-bottom:0.75rem;">
            এখনই আপডেট করুন
        </a>
        <button id="dismiss-update-btn" onclick="dismissAppUpdate()" style="background:none; border:none; color:#64748b; font-size:0.8rem; cursor:pointer; padding:0.5rem;">
            পরে করবো
        </button>
    </div>
</div>

<script>
(function() {
    var MIN_VERSION = {{ $minAppVersion }};
    var ua = navigator.userAgent;
    var appMatch = ua.match(/AlliedGroupApp\/(\d+)/);
    
    // Debug: Log full UA string
    console.log('=== APP UPDATE CHECK ===');
    console.log('User-Agent:', ua);
    console.log('Min Required Version:', MIN_VERSION);
    
    // Only show popup if app version is old
    var needsUpdate = false;
    var currentVersion = null;
    
    if (appMatch) {
        // App with version tag - only show if outdated
        currentVersion = parseInt(appMatch[1]);
        console.log('Detected app version:', currentVersion);
        if (currentVersion < MIN_VERSION) {
            needsUpdate = true;
            console.log('❌ Version is OLD - Update needed!');
        } else {
            console.log('✅ App is up to date');
        }
    } else {
        // No AlliedGroupApp tag - check if it's the old Android WebView app
        var isAndroid = ua.indexOf('Android') !== -1;
        var hasWebViewMarker = ua.indexOf('wv)') !== -1 || ua.indexOf('Version/') !== -1;
        var hasAppIndicators = ua.indexOf('SavingsApp') !== -1 || ua.indexOf('AlliedGroup') !== -1;
        
        console.log('No version tag found. Checks:');
        console.log('- Is Android?', isAndroid);
        console.log('- Has WebView markers?', hasWebViewMarker);
        console.log('- Has app name indicators?', hasAppIndicators);
        
        // Show popup if it's Android with WebView markers OR if it has old app name
        if (isAndroid && (hasWebViewMarker || hasAppIndicators)) {
            needsUpdate = true;
            console.log('❌ Old Android app/WebView detected - Update needed!');
        } else {
            console.log('ℹ️ Regular browser or desktop - no update needed');
        }
    }
    
    if (needsUpdate) {
        // Fetch force update settings from server
        fetch('{{ route("app.update.settings") }}')
            .then(function(response) { return response.json(); })
            .then(function(settings) {
                var forceUpdate = settings.force_update || false;
                console.log('Force update enabled:', forceUpdate);
                
                // If force update is enabled, don't check dismiss status
                if (forceUpdate) {
                    console.log('🚨 FORCE UPDATE - showing popup without dismiss option');
                    showUpdatePopup(true);
                    return;
                }
                
                // Check if user already dismissed (don't show again for 24 hours)
                // Use version-specific key so new updates show even if old version was dismissed
                var dismissKey = 'app_update_dismissed_v' + MIN_VERSION;
                var dismissed = localStorage.getItem(dismissKey);
                var dismissedTime = dismissed ? parseInt(dismissed) : 0;
                var hoursSinceDismissed = (Date.now() - dismissedTime) / (1000 * 60 * 60);
                
                console.log('Update needed. Dismiss key:', dismissKey);
                console.log('Dismissed time:', dismissed ? new Date(dismissedTime) : 'Never');
                console.log('Hours since dismissed:', hoursSinceDismissed.toFixed(2));
                
                if (dismissed && (Date.now() - dismissedTime) < 24 * 60 * 60 * 1000) {
                    console.log('⏰ Update popup dismissed for v' + MIN_VERSION + ', will show again after 24h');
                    console.log('To force show popup, run: localStorage.removeItem("' + dismissKey + '")');
                    return;
                }
                
                showUpdatePopup(false);
            })
            .catch(function(error) {
                console.error('Failed to fetch update settings:', error);
                // If fetch fails, show popup with dismiss option (default behavior)
                showUpdatePopup(false);
            });
    } else {
        // App is up to date - clear any old dismissed flags
        for (var i = 1; i <= MIN_VERSION; i++) {
            localStorage.removeItem('app_update_dismissed_v' + i);
        }
        localStorage.removeItem('app_update_dismissed'); // Old key
        console.log('✅ No update needed, cleared all dismiss flags');
    }
    console.log('======================');
})();

function showUpdatePopup(forceUpdate) {
    var overlay = document.getElementById('app-update-overlay');
    var dismissBtn = document.getElementById('dismiss-update-btn');
    
    if (overlay) {
        console.log('🔔 SHOWING UPDATE POPUP (Force: ' + forceUpdate + ')');
        overlay.style.display = 'flex';
        
        // Hide dismiss button if force update is enabled
        if (forceUpdate && dismissBtn) {
            dismissBtn.style.display = 'none';
            console.log('Dismiss button hidden - force update enabled');
        }
    } else {
        console.error('❌ Update overlay element not found!');
    }
}

function dismissAppUpdate() {
    var MIN_VERSION = {{ $minAppVersion }};
    var dismissKey = 'app_update_dismissed_v' + MIN_VERSION;
    var now = Date.now();
    localStorage.setItem(dismissKey, now.toString());
    console.log('Update popup for v' + MIN_VERSION + ' dismissed at:', new Date(now));
    console.log('Will show again after 24 hours');
    console.log('Dismiss key used:', dismissKey);
    document.getElementById('app-update-overlay').style.display = 'none';
}
</script>
