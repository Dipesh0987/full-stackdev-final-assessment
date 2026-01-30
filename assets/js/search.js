document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('searchBox');
    const suggestions = document.getElementById('suggestions');
    
    if (!searchBox || !suggestions) return;
    
    let debounceTimer;
    
    searchBox.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        
        const query = this.value.trim();
        
        if (query.length < 2) {
            suggestions.innerHTML = '';
            suggestions.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });
    
    searchBox.addEventListener('focus', function() {
        const query = this.value.trim();
        if (query.length >= 2 && suggestions.children.length > 0) {
            suggestions.style.display = 'block';
        }
    });
    
    // when clicked outside suggestion disappears
    document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });
    
    function fetchSuggestions(query) {
        fetch('ajax_search.php?q=' + encodeURIComponent(query))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                suggestions.innerHTML = '';
                
                if (data.length > 0) {
                    suggestions.style.display = 'block';
                    
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = item;
                        li.style.padding = '10px 15px';
                        li.style.cursor = 'pointer';
                        li.style.borderBottom = '1px solid #eee';
                        li.style.transition = 'background-color 0.2s';
                        
                        li.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#f8f9fa';
                        });
                        
                        li.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = '';
                        });
                        
                        li.addEventListener('click', function() {
                            searchBox.value = item;
                            suggestions.innerHTML = '';
                            suggestions.style.display = 'none';
                        });
                        
                        suggestions.appendChild(li);
                    });
                } else {
                    suggestions.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                suggestions.style.display = 'none';
            });
    }
    
    // Handle keyboard navigation
    searchBox.addEventListener('keydown', function(e) {
        const items = suggestions.querySelectorAll('li');
        
        if (items.length === 0) return;
        
        let currentIndex = -1;
        items.forEach((item, index) => {
            if (item.style.backgroundColor === 'rgb(248, 249, 250)') {
                currentIndex = index;
            }
        });
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (currentIndex < items.length - 1) {
                    if (currentIndex >= 0) {
                        items[currentIndex].style.backgroundColor = '';
                    }
                    items[currentIndex + 1].style.backgroundColor = '#f8f9fa';
                }
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                if (currentIndex > 0) {
                    items[currentIndex].style.backgroundColor = '';
                    items[currentIndex - 1].style.backgroundColor = '#f8f9fa';
                }
                break;
                
            case 'Enter':
                if (currentIndex >= 0) {
                    e.preventDefault();
                    searchBox.value = items[currentIndex].textContent;
                    suggestions.style.display = 'none';
                }
                break;
                
            case 'Escape':
                suggestions.style.display = 'none';
                break;
        }
    });
});