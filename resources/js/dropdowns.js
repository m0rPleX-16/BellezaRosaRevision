// Dropdown functionality
class Dropdown {
    constructor(element) {
        this.element = element;
        this.targetId = this.element.getAttribute('data-dropdown-toggle');
        this.target = document.getElementById(this.targetId);
        this.isOpen = false;
        
        if (!this.target) return;
        
        // Toggle on button click
        this.element.addEventListener('click', (e) => this.toggle(e));
        
        // Close when clicking outside
        document.addEventListener('click', (e) => this.handleClickOutside(e));
    }
    
    toggle(e) {
        e.stopPropagation();
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            this.target.classList.remove('hidden');
            this.element.setAttribute('aria-expanded', 'true');
            
            // Close other dropdowns
            Dropdown.closeAll(this);
        } else {
            this.close();
        }
    }
    
    close() {
        if (this.target) {
            this.target.classList.add('hidden');
        }
        this.isOpen = false;
        this.element.setAttribute('aria-expanded', 'false');
    }
    
    handleClickOutside(e) {
        if (!this.element.contains(e.target) && !this.target.contains(e.target)) {
            this.close();
        }
    }
    
    static closeAll(except = null) {
        document.querySelectorAll('[data-dropdown]').forEach(dropdown => {
            const dropdownId = dropdown.id;
            const button = document.querySelector(`[data-dropdown-toggle="${dropdownId}"]`);
            
            if (button && button.dropdown && button.dropdown !== except) {
                button.dropdown.close();
            }
        });
    }
}

// Initialize dropdowns when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize all dropdown toggles
    document.querySelectorAll('[data-dropdown-toggle]').forEach(toggle => {
        toggle.dropdown = new Dropdown(toggle);
    });
    
    // Close dropdowns when clicking the escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            Dropdown.closeAll();
        }
    });
});

// Make the Dropdown class available globally
window.Dropdown = Dropdown;
