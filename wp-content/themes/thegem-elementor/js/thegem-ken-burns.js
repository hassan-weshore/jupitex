document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelectorAll('.thegem-ken-burns-bg');

    if ('IntersectionObserver' in window) {
        let intersectionObserver = new IntersectionObserver(function(entries, imgObserver) {
            entries.forEach(function(entry) {
                const item = entry.target;

                if (entry.isIntersecting) {
                    item.classList.add('thegem-ken-burns-inited');
                } else {
                    item.classList.remove('thegem-ken-burns-inited');
                }
            });
        });

        items.forEach(function(item) {
            intersectionObserver.observe(item);
        });
    } else {
        items.forEach(function(item) {
            item.classList.add('thegem-ken-burns-inited');
        });
    }
});