
window.onload = function() {

    let select_lieu = document.getElementById('sortie_lieu_nom');
    let select_ville = document.getElementById('sortie_lieu_ville');

    select_ville.onchange = function () {
        // Vide les options
        select_lieu.innerHTML = "";
        avoirLieuDansVille(select_ville.value);
    }
    select_lieu.onchange = function () {
        miseAJourLieu(select_lieu.value);
    }

    function avoirLieuDansVille(id_ville) {
        axios
            .get('../api/lieu/ville/' + id_ville)
            .then((response) => {
                for (let lieu of response.data) {
                    avoirLieuAvecId(lieu.id);
                }
            })
    }

    function avoirLieuAvecId(id_lieu) {
        axios
            .get('../api/lieu/' + id_lieu)
            .then((response) => {
                insererLieuDansPage(response.data);
            })
    }

    function insererLieuDansPage(lieu) {
        // Creer option
        let option = document.createElement('option');
        option.setAttribute('value', lieu.id);
        option.textContent = lieu.nom

        // Si la premiere option
        if (select_lieu.innerHTML == "") {
            // Selectionner la première option
            option.setAttribute('selected', true);
            // Remplir les autres champs
            document.getElementById('sortie_lieu_rue').value = lieu.rue;
            document.getElementById('sortie_lieu_latitude').value = lieu.latitude;
            document.getElementById('sortie_lieu_longitude').value = lieu.longitude;
        }
        // Ajouter le contenue à la page
        select_lieu.appendChild(option);
    }

    function miseAJourLieu(id) {
        axios
            .get('../api/lieu/' + id)
            .then((response) => {
                document.getElementById('sortie_lieu_rue').value = response.data.rue;
                document.getElementById('sortie_lieu_latitude').value = response.data.latitude;
                document.getElementById('sortie_lieu_longitude').value = response.data.longitude;
            })
    }
}