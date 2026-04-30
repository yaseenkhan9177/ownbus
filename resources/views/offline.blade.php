<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title>OwnBus - Offline</title>
<style>
body {
    background: #0A0F1E;
    color: white;
    font-family: 'DM Sans', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
}
.container {
    text-align: center;
    padding: 2rem;
}
.icon { font-size: 5rem; margin-bottom: 1rem; }
h1 { font-size: 2rem; color: #F59E0B; }
p { color: #9CA3AF; margin: 1rem 0; }
.retry-btn {
    background: #00BCD4;
    color: white;
    border: none;
    padding: 12px 32px;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 1rem;
}
.offline-features {
    background: #111827;
    border: 1px solid #1F2937;
    border-radius: 12px;
    padding: 1.5rem;
    margin: 2rem auto;
    max-width: 400px;
    text-align: left;
}
.feature { 
    display: flex; 
    align-items: center; 
    gap: 0.5rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #1F2937;
}
</style>
</head>
<body>
<div class="container">
    <div class="icon">📡</div>
    <h1>You're Offline</h1>
    <p>No internet connection detected.<br>
    Don't worry - your data is safe!</p>
    
    <div class="offline-features">
        <p style="color:#F59E0B; font-weight:bold; 
           margin-bottom:1rem;">
           What you can still do:
        </p>
        <div class="feature">
            ✅ View cached dashboard data
        </div>
        <div class="feature">
            ✅ Fill rental forms (saves locally)
        </div>
        <div class="feature">
            ✅ View vehicle list
        </div>
        <div class="feature">
            ✅ View customer list
        </div>
        <div class="feature">
            🔄 Data syncs when back online
        </div>
    </div>
    
    <button class="retry-btn" 
        onclick="window.location.reload()">
        🔄 Try Again
    </button>
    
    <p style="margin-top:2rem; font-size:0.8rem;">
        OwnBus Fleet Management • ownbus.software
    </p>
</div>

<script>
// Auto retry when online
window.addEventListener('online', () => {
    window.location.href = '/company/dashboard';
});
</script>
</body>
</html>
