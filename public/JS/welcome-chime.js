document.addEventListener('DOMContentLoaded', function() {
    console.log('Welcome chime script loaded');
    console.log('First login status:', window.isFirstLogin);
    
    // Check if this is the first login and we haven't played the sound yet in this session
    if (window.isFirstLogin && !localStorage.getItem('welcomeChimePlayed')) {
        console.log('Playing welcome chime...');
        const audio = new Audio('/sounds/welcome-chime.mp3');
        audio.play().then(() => {
            console.log('Welcome chime played successfully');
            // Mark the chime as played in this session
            localStorage.setItem('welcomeChimePlayed', 'true');
        }).catch(function(error) {
            console.error("Audio playback failed:", error);
        });
    } else {
        console.log('Not first login or chime already played, skipping welcome chime');
    }
});
