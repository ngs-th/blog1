<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<script>
// Global error handler for NotSupportedError
window.addEventListener('error', function(e) {
    if (e.error && e.error.name === 'NotSupportedError' && e.error.message.includes('attributes')) {
        console.warn('NotSupportedError suppressed:', e.error.message);
        e.preventDefault();
        return false;
    }
});

// Early compatibility fixes
(function() {
    // Override problematic DOM methods early
    const originalSetAttribute = Element.prototype.setAttribute;
    const originalRemoveAttribute = Element.prototype.removeAttribute;
    
    Element.prototype.setAttribute = function(name, value) {
        try {
            return originalSetAttribute.call(this, name, value);
        } catch (e) {
            if (e.name === 'NotSupportedError') {
                console.warn('setAttribute error suppressed:', name, value, e.message);
                return;
            }
            throw e;
        }
    };
    
    Element.prototype.removeAttribute = function(name) {
        try {
            return originalRemoveAttribute.call(this, name);
        } catch (e) {
            if (e.name === 'NotSupportedError') {
                console.warn('removeAttribute error suppressed:', name, e.message);
                return;
            }
            throw e;
        }
    };
})();
</script>

@fluxScripts
<script>
// Safari/Electron compatibility fixes
(function() {
    // Polyfill for Popover API
    if (!HTMLElement.prototype.showPopover) {
        HTMLElement.prototype.showPopover = function() {
            try {
                this.style.display = 'block';
                this.setAttribute('data-popover-open', 'true');
            } catch (e) {
                console.warn('Popover polyfill error:', e);
            }
        };
        HTMLElement.prototype.hidePopover = function() {
            try {
                this.style.display = 'none';
                this.removeAttribute('data-popover-open');
            } catch (e) {
                console.warn('Popover polyfill error:', e);
            }
        };
        HTMLElement.prototype.togglePopover = function() {
            try {
                if (this.hasAttribute('data-popover-open')) {
                    this.hidePopover();
                } else {
                    this.showPopover();
                }
            } catch (e) {
                console.warn('Popover polyfill error:', e);
            }
        };
    }
    
    // Comprehensive DOM method wrapping to handle NotSupportedError
    const originalSetAttribute = Element.prototype.setAttribute;
    const originalRemoveAttribute = Element.prototype.removeAttribute;
    const originalHasAttribute = Element.prototype.hasAttribute;
    const originalGetAttribute = Element.prototype.getAttribute;
    
    Element.prototype.setAttribute = function(name, value) {
        try {
            return originalSetAttribute.call(this, name, value);
        } catch (e) {
            if (e.name === 'NotSupportedError') {
                console.warn('setAttribute NotSupportedError suppressed:', name, value);
                return;
            }
            throw e;
        }
    };
    
    Element.prototype.removeAttribute = function(name) {
        try {
            return originalRemoveAttribute.call(this, name);
        } catch (e) {
            if (e.name === 'NotSupportedError') {
                console.warn('removeAttribute NotSupportedError suppressed:', name);
                return;
            }
            throw e;
        }
    };
    
    Element.prototype.hasAttribute = function(name) {
        try {
            return originalHasAttribute.call(this, name);
        } catch (e) {
            if (e.name === 'NotSupportedError') {
                console.warn('hasAttribute NotSupportedError suppressed:', name);
                return false;
            }
            throw e;
        }
    };
    
    Element.prototype.getAttribute = function(name) {
        try {
            return originalGetAttribute.call(this, name);
        } catch (e) {
            if (e.name === 'NotSupportedError') {
                console.warn('getAttribute NotSupportedError suppressed:', name);
                return null;
            }
            throw e;
        }
    };
    
    // Fix for ui-radio-group custom elements
    function initializeRadioGroups() {
        const radioGroups = document.querySelectorAll('ui-radio-group');
        
        radioGroups.forEach(group => {
            if (group.hasAttribute('data-initialized')) return;
            group.setAttribute('data-initialized', 'true');
            
            const radios = group.querySelectorAll('ui-radio');
            const model = group.getAttribute('x-model');
            
            // Handle radio selection
            radios.forEach(radio => {
                radio.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const value = this.getAttribute('value');
                    
                    // Remove checked state from all radios
                    radios.forEach(r => {
                        r.removeAttribute('data-checked');
                        r.setAttribute('tabindex', '-1');
                    });
                    
                    // Add checked state to clicked radio
                    this.setAttribute('data-checked', '');
                    this.setAttribute('tabindex', '0');
                    
                    // Update theme system
                     if (model && model.includes('$flux.appearance')) {
                         // Update Flux appearance system
                         if (window.$flux) {
                             window.$flux.appearance = value;
                         }
                         
                         // Update Alpine.js store if available
                         if (window.Alpine && Alpine.store) {
                             try {
                                 Alpine.store('flux', { appearance: value });
                             } catch (e) {
                                 console.warn('Alpine store update failed:', e);
                             }
                         }
                         
                         // Update document class for theme
                         const htmlElement = document.documentElement;
                         if (value === 'system') {
                             htmlElement.removeAttribute('class');
                             // Detect system preference
                             const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                             if (prefersDark) {
                                 htmlElement.classList.add('dark');
                             }
                         } else {
                             htmlElement.className = value;
                         }
                         
                         // Store preference
                         try {
                             localStorage.setItem('flux-appearance', value);
                         } catch (e) {
                             console.warn('localStorage not available:', e);
                         }
                     }
                    
                    // Dispatch custom event
                    group.dispatchEvent(new CustomEvent('change', {
                        detail: { value },
                        bubbles: true
                    }));
                });
                
                // Handle keyboard navigation
                radio.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
            
            // Initialize with current theme
            const currentTheme = document.documentElement.className || 'system';
            const currentRadio = group.querySelector(`ui-radio[value="${currentTheme}"]`);
            if (currentRadio) {
                currentRadio.setAttribute('data-checked', '');
                currentRadio.setAttribute('tabindex', '0');
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeRadioGroups);
    } else {
        initializeRadioGroups();
    }
    
    // Re-initialize when new content is added (for SPA navigation)
     function setupMutationObserver() {
         if (document.body) {
             const observer = new MutationObserver(function(mutations) {
                 mutations.forEach(function(mutation) {
                     if (mutation.addedNodes.length > 0) {
                         setTimeout(initializeRadioGroups, 100);
                     }
                 });
             });
             
             observer.observe(document.body, {
                 childList: true,
                 subtree: true
             });
         }
     }
     
     // Setup observer when DOM is ready
     if (document.readyState === 'loading') {
         document.addEventListener('DOMContentLoaded', setupMutationObserver);
     } else {
         setupMutationObserver();
     }
})();
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
