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
    const sitemapForm = document.getElementById('change-sitemap-form');
    const sitemapSwitch = document.getElementById('sitemap_enable');
    const sitemapImagesSwitch = document.getElementById('sitemap_images_enable');

    const successNotification = document.getElementById('notification-sitemap-success');
    const errorNotification = document.getElementById('notification-sitemap-error');

    sitemapForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const sitemapEnabled = sitemapSwitch.getAttribute('aria-checked') === 'true';
        const sitemapImagesEnabled = sitemapImagesSwitch.getAttribute('aria-checked') === 'true';

        const data = {
            sitemap_enable: sitemapEnabled,
            sitemap_images_enable: sitemapImagesEnabled
        };

        try {
    const response = await fetch('../api/change_sitesettings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const text = await response.text(); // holt Rohtext
    console.log('Raw response:', text);

    const result = JSON.parse(text); // hier evtl. Fehler
    if (result.success) {
        successNotification.classList.remove('hidden');
        errorNotification.classList.add('hidden');
    } else {
        errorNotification.classList.remove('hidden');
        successNotification.classList.add('hidden');
    }
} catch (error) {
    console.error('Fehler beim Parsen der Antwort:', error);
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

document.addEventListener('DOMContentLoaded', function () {
    const themeForm = document.getElementById('change-theme');
    const successNotification = document.getElementById('notification-theme-success');
    const errorNotification = document.getElementById('notification-theme-error');
    const hiddenThemeInput = document.getElementById('theme');

    if (themeForm) {
        themeForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const selectedTheme = hiddenThemeInput.value.trim();

            const data = {
                theme: selectedTheme
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
    }
});