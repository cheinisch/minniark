console.log("sync_exifdata.js geladen");
document.addEventListener("DOMContentLoaded",()=>{
    console.log("Add Eventlistener");
    document.getElementById("update-exif").addEventListener("click", () => {
        const fileName = document.getElementById("rating-stars")?.dataset.filename;
        if (!fileName) {
        alert("Kein Dateiname gefunden.");
        console.log("No File Name");
        return;
        }
    
        fetch(`${window.location.origin}/api/sync_exif.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ filename: fileName })
        })
        .then(async res => {
        const text = await res.text(); // 🪄 lese Response als Text
        try {
            const json = JSON.parse(text);
            console.log("JSON Response:", json);
            return json; //  wichtig für den nächsten then()
        } catch (e) {
            console.error("Kein valides JSON:", text);
            throw new Error("Ungültige Serverantwort");
        }
        })
        .then((data) => {
        if (data.success) {
            alert("Metadaten erfolgreich aktualisiert!");
            location.reload(); // optional
        } else {
            alert("Fehler: " + (data.error || "Unbekannter Fehler"));
        }
        })
        .catch((err) => {
        console.error("Netzwerkfehler:", err);
        alert("Netzwerkfehler beim Laden der EXIF-Daten.");
        });
    })
});

  