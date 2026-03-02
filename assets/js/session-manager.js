/**
 * Session Manager - Tab-Specific Logout
 * Logs out when individual tab is closed (not just browser)
 * Uses sessionStorage (tab-specific) + beforeunload event
 */

(function() {
    'use strict';
    
    // Configuration
    const INACTIVITY_TIMEOUT = 15 * 60 * 1000; // 15 minutes
    const WARNING_TIME = 2 * 60 * 1000; // 2 minutes before timeout
    const CHECK_INTERVAL = 60 * 1000; // Check every minute
    const TAB_ID = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    
    let inactivityTimer = null;
    let warningTimer = null;
    let lastActivity = Date.now();
    let warningShown = false;
    let isLoggingOut = false;
    
    /**
     * Initialize tab tracking
     */
    function initTabTracking() {
        // Register this tab
        sessionStorage.setItem('tab_id', TAB_ID);
        sessionStorage.setItem('tab_active', 'true');
        sessionStorage.setItem('tab_opened', Date.now().toString());
        
        // Mark in localStorage that we have an active tab
        const activeTabs = JSON.parse(localStorage.getItem('active_tabs') || '[]');
        activeTabs.push(TAB_ID);
        localStorage.setItem('active_tabs', JSON.stringify(activeTabs));
    }
    
    /**
     * Remove tab from tracking
     */
    function removeTabTracking() {
        const tabId = sessionStorage.getItem('tab_id');
        if (tabId) {
            const activeTabs = JSON.parse(localStorage.getItem('active_tabs') || '[]');
            const updatedTabs = activeTabs.filter(id => id !== tabId);
            localStorage.setItem('active_tabs', JSON.stringify(updatedTabs));
        }
    }
    
    /**
     * Check if this is the last tab
     */
    function isLastTab() {
        const activeTabs = JSON.parse(localStorage.getItem('active_tabs') || '[]');
        return activeTabs.length <= 1;
    }
    
    /**
     * Reset the inactivity timer
     */
    function resetInactivityTimer() {
        lastActivity = Date.now();
        warningShown = false;
        
        if (inactivityTimer) clearTimeout(inactivityTimer);
        if (warningTimer) clearTimeout(warningTimer);
        
        hideWarning();
        
        warningTimer = setTimeout(showWarning, INACTIVITY_TIMEOUT - WARNING_TIME);
        inactivityTimer = setTimeout(logoutDueToInactivity, INACTIVITY_TIMEOUT);
    }
    
    /**
     * Show inactivity warning
     */
    function showWarning() {
        if (warningShown) return;
        warningShown = true;
        
        const modal = document.createElement('div');
        modal.id = 'inactivity-warning-modal';
        modal.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7); display: flex;
            align-items: center; justify-content: center; z-index: 10000;
        `;
        
        const content = document.createElement('div');
        content.style.cssText = `
            background: white; padding: 30px; border-radius: 8px;
            max-width: 400px; text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        `;
        
        content.innerHTML = `
            <div style="font-size: 48px; margin-bottom: 20px;">⏰</div>
            <h2 style="margin: 0 0 15px 0; color: #333;">Session Timeout Warning</h2>
            <p style="color: #666; margin-bottom: 20px;">
                You will be logged out in <strong id="countdown">2:00</strong> due to inactivity.
            </p>
            <button id="stay-logged-in-btn" style="
                background: #4CAF50; color: white; border: none;
                padding: 12px 30px; border-radius: 4px; font-size: 16px;
                cursor: pointer; margin-right: 10px;
            ">Stay Logged In</button>
            <button id="logout-now-btn" style="
                background: #f44336; color: white; border: none;
                padding: 12px 30px; border-radius: 4px; font-size: 16px;
                cursor: pointer;
            ">Logout Now</button>
        `;
        
        modal.appendChild(content);
        document.body.appendChild(modal);
        
        let secondsLeft = 120;
        const countdownEl = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
            secondsLeft--;
            const minutes = Math.floor(secondsLeft / 60);
            const seconds = secondsLeft % 60;
            countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            if (secondsLeft <= 0) clearInterval(countdownInterval);
        }, 1000);
        
        document.getElementById('stay-logged-in-btn').addEventListener('click', () => {
            clearInterval(countdownInterval);
            resetInactivityTimer();
        });
        
        document.getElementById('logout-now-btn').addEventListener('click', () => {
            clearInterval(countdownInterval);
            logout();
        });
    }
    
    /**
     * Hide warning modal
     */
    function hideWarning() {
        const modal = document.getElementById('inactivity-warning-modal');
        if (modal) modal.remove();
    }
    
    /**
     * Logout due to inactivity
     */
    function logoutDueToInactivity() {
        hideWarning();
        
        const message = document.createElement('div');
        message.style.cssText = `
            position: fixed; top: 20px; right: 20px;
            background: #f44336; color: white; padding: 15px 20px;
            border-radius: 4px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 10001;
        `;
        message.textContent = 'Logged out due to inactivity';
        document.body.appendChild(message);
        
        setTimeout(logout, 1000);
    }
    
    /**
     * Perform logout
     */
    function logout() {
        if (isLoggingOut) return;
        isLoggingOut = true;
        
        // Remove tab tracking
        removeTabTracking();
        
        // Clear storage
        sessionStorage.clear();
        
        // Redirect to logout
        window.location.href = getBaseUrl() + 'simple_portal?action=logout';
    }
    
    /**
     * Get base URL
     */
    function getBaseUrl() {
        const base = document.querySelector('base');
        if (base) return base.href;
        return window.location.origin + '/';
    }
    
    /**
     * Handle before unload (tab close)
     */
    function handleBeforeUnload(e) {
        // Don't logout if user is just refreshing or navigating within the app
        const isRefresh = performance.navigation.type === 1;
        const isInternalNav = e.currentTarget.location.href.includes(window.location.hostname);
        
        if (isRefresh || isInternalNav) {
            // Just update tab tracking
            sessionStorage.setItem('tab_closing', 'false');
            return;
        }
        
        // Mark that tab is closing
        sessionStorage.setItem('tab_closing', 'true');
        removeTabTracking();
        
        // Send logout request using synchronous XHR (only reliable method for beforeunload)
        try {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', getBaseUrl() + 'simple_portal/logout_on_close', false); // false = synchronous
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('tab_close=true&tab_id=' + TAB_ID);
        } catch (err) {
            console.error('Logout request failed:', err);
        }
    }
    
    /**
     * Handle page visibility change
     */
    function handleVisibilityChange() {
        if (document.hidden) {
            sessionStorage.setItem('tab_hidden_time', Date.now().toString());
        } else {
            const hiddenTime = sessionStorage.getItem('tab_hidden_time');
            if (hiddenTime) {
                const timeHidden = Date.now() - parseInt(hiddenTime);
                // If hidden for more than 30 minutes, logout
                if (timeHidden > 30 * 60 * 1000) {
                    logout();
                    return;
                }
            }
            resetInactivityTimer();
        }
    }
    
    /**
     * Handle page load
     */
    function handlePageLoad() {
        // Check if tab was closing
        const wasClosing = sessionStorage.getItem('tab_closing');
        
        if (wasClosing === 'true') {
            // Tab was closed and reopened - logout
            sessionStorage.clear();
            logout();
            return;
        }
        
        // Initialize tab tracking
        initTabTracking();
        
        // Start inactivity timer
        resetInactivityTimer();
    }
    
    /**
     * Handle page unload (final cleanup)
     */
    function handleUnload() {
        removeTabTracking();
    }
    
    /**
     * Initialize session manager
     */
    function init() {
        const isAuthenticated = document.body.classList.contains('authenticated') || 
                               document.querySelector('.sidebar') !== null;
        
        if (!isAuthenticated) return;
        
        // Activity events
        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        let throttleTimer = null;
        
        activityEvents.forEach(event => {
            document.addEventListener(event, () => {
                if (!throttleTimer) {
                    throttleTimer = setTimeout(() => {
                        resetInactivityTimer();
                        throttleTimer = null;
                    }, 1000);
                }
            }, true);
        });
        
        // Handle visibility change
        document.addEventListener('visibilitychange', handleVisibilityChange);
        
        // Handle before unload (tab close)
        window.addEventListener('beforeunload', handleBeforeUnload);
        
        // Handle unload (final cleanup)
        window.addEventListener('unload', handleUnload);
        
        // Handle page load
        handlePageLoad();
        
        // Periodic session check
        setInterval(() => {
            fetch(getBaseUrl() + 'simple_portal/check_session', {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (!data.valid) logout();
            })
            .catch(err => console.error('Session check failed:', err));
        }, CHECK_INTERVAL);
    }
    
    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
