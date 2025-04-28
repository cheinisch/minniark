document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('change-sitedata-form');
    const successNotification = document.getElementById('notification-success-user');
    const errorNotification = document.getElementById('notification-error-user');
    const languageButton = document.querySelector('[aria-haspopup="listbox-language"]');
    const languageText = languageButton?.querySelector('span.truncate');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        const data = {
            site_name: formData.get('site-name'),
            site_description: formData.get('site-decription'),
            language: languageText ? languageText.innerText.trim() : 'en' // falls Sprache nicht auswählbar, Default auf 'en'
        };

        try {
            const response = await fetch('../api/change_sitesettings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                successNotification.classList.remove('hidden');
                errorNotification.classList.add('hidden');
            } else {
                errorNotification.classList.remove('hidden');
                successNotification.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorNotification.classList.remove('hidden');
            successNotification.classList.add('hidden');
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const imageSizeForm = document.getElementById('change-image-size');
    const successNotification = document.getElementById('notification-success');
    const errorNotification = document.getElementById('notification-error');

    imageSizeForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(imageSizeForm);

        const data = {
            image_size: formData.get('image_size')
        };

        try {
            const response = await fetch('../api/change_sitesettings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                successNotification.classList.remove('hidden');
                errorNotification.classList.add('hidden');
            } else {
                errorNotification.classList.remove('hidden');
                successNotification.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorNotification.classList.remove('hidden');
            successNotification.classList.add('hidden');
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const timelineForm = document.getElementById('change-timeline-form');
    const successNotification = document.getElementById('notification-timeline-success');
    const errorNotification = document.getElementById('notification-timeline-error');

    timelineForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Status der beiden Switch-Buttons holen
        const timelineEnable = document.getElementById('timline_enable').getAttribute('aria-checked') === 'true';
        const timelineGroup = document.getElementById('timline_group').getAttribute('aria-checked') === 'true';

        const data = {
            timeline_enable: timelineEnable,
            timeline_group_by_date: timelineGroup
        };

        try {
            const response = await fetch('../api/change_sitesettings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                successNotification.classList.remove('hidden');
                errorNotification.classList.add('hidden');
            } else {
                errorNotification.classList.remove('hidden');
                successNotification.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorNotification.classList.remove('hidden');
            successNotification.classList.add('hidden');
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const mapForm = document.getElementById('change-map-form');
    const successNotification = document.getElementById('notification-map-success');
    const errorNotification = document.getElementById('notification-map-error');

    mapForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const mapEnable = document.getElementById('map_enable').getAttribute('aria-checked') === 'true';

        const data = {
            map_enable: mapEnable
        };

        try {
            const response = await fetch('../api/change_sitesettings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                successNotification.classList.remove('hidden');
                errorNotification.classList.add('hidden');
            } else {
                errorNotification.classList.remove('hidden');
                successNotification.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorNotification.classList.remove('hidden');
            successNotification.classList.add('hidden');
        }
    });
});
