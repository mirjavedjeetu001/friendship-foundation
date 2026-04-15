{{-- App Update Popup - Shows when user has an outdated Android app --}}
@php $minAppVersion = 2; @endphp
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
        <button onclick="dismissAppUpdate()" style="background:none; border:none; color:#64748b; font-size:0.8rem; cursor:pointer; padding:0.5rem;">
            পরে করবো
        </button>
    </div>
</div>

<script>
(function() {
    var MIN_VERSION = {{ $minAppVersion }};
    var ua = navigator.userAgent;
    var appMatch = ua.match(/AlliedGroupApp\/(\d+)/);
    
    // Only show popup if AlliedGroupApp is in UA but version is old
    // Updated app (v2+) will have AlliedGroupApp/2 - no popup
    // Old app without AlliedGroupApp in UA - show popup only if it looks like Android WebView
    var needsUpdate = false;
    
    if (appMatch) {
        // App with version tag - only show if outdated
        if (parseInt(appMatch[1]) < MIN_VERSION) {
            needsUpdate = true;
        }
    } else {
        // No AlliedGroupApp tag - check if it's the old Android WebView app
        // Old WebView has 'wv)' in UA (note the closing paren)
        var isOldWebView = ua.indexOf('wv)') !== -1 && ua.indexOf('Android') !== -1;
        if (isOldWebView) {
            needsUpdate = true;
        }
    }
    
    if (needsUpdate) {
        // Check if user already dismissed (don't show again for 24 hours)
        var dismissed = localStorage.getItem('app_update_dismissed');
        if (dismissed && (Date.now() - parseInt(dismissed)) < 24 * 60 * 60 * 1000) {
            return;
        }
        var overlay = document.getElementById('app-update-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
    }
})();

function dismissAppUpdate() {
    localStorage.setItem('app_update_dismissed', Date.now().toString());
    document.getElementById('app-update-overlay').style.display = 'none';
}
</script>
