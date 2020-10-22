window.onload = function() {
    let root_path = ROOT_PATH;
    // Retire le slash de fin
    root_path = root_path.slice(0, root_path.length - 1);

    let select_lieu = document.getElementById('sortie_lieu');
    let select_ville = document.getElementById('sortie_formLieu_ville');

    if(select_lieu && select_ville){
        // A la sélection de la ville
        select_ville.onchange = function () {
            // Vide les options
            select_lieu.innerHTML = "";
            avoirLieuDansVille(select_ville.value);
        }
        // A la selection du lieu
        select_lieu.onchange = function () {
            miseAJourInfosLieu(select_lieu.value);
        }

        initialisation();
    }

    // Initialisation
    function initialisation(){
        select_lieu.innerHTML = "";
        avoirLieuDansVille(select_ville.querySelector('option').value);
    }

    function avoirLieuDansVille(id_ville) {
        axios
            .get(root_path + '/api/lieu/ville/' + id_ville)

            .then((response) => {
                for (let lieu of response.data) {
                    avoirLieuAvecId(lieu.id);
                }
            })
    }

    function avoirLieuAvecId(id_lieu) {
        axios
            .get(root_path + '/api/lieu/' + id_lieu)
            .then((response) => {
                insererLieuDansSelect(response.data);
            })
    }

    function insererLieuDansSelect(lieu) {
        // Creer option
        let option = document.createElement('option');
        option.setAttribute('value', lieu.id);
        option.textContent = lieu.nom

        // Si la premiere option
        if (select_lieu.innerHTML == "") {
            // Selectionner la première option
            // option.setAttribute('selected', '');
            miseAJourInfosLieu(lieu.id);
        }
        // Ajouter les options à la liste
        select_lieu.appendChild(option);
    }

    function miseAJourInfosLieu(id) {
        axios
            .get(root_path + '/api/lieu/' + id)
            .then((response) => {
                // Remplir les autres champs de la page
                document.getElementById('sortie_formLieu_rue').value = response.data.rue;
                document.getElementById('sortie_formLieu_latitude').value = response.data.latitude == undefined ? 'Non renseigné' : response.data.latitude;
                document.getElementById('sortie_formLieu_longitude').value = response.data.longitude == undefined ? 'Non renseigné' : response.data.longitude;
            })
    }

    //
    // Creer un Lieu
    //
    let formulaire_lieu = document.getElementById('form_ajouter_lieu');
    if(formulaire_lieu){
        formulaire_lieu.onsubmit = function (e){
            e.preventDefault();
            // Creer un lieu
            let formData = new FormData(formulaire_lieu)
            axios({
                method: 'post',
                url: root_path + '/api/lieu/creer',
                data: formData
            }).then(()=>{

                // Met à jour l'affichage
                initialisation();

                // Fermer la modal
                $('#ajouterLieu').modal('toggle');

                // Afficher un message à l'utilisateur
                let div = document.createElement('div');
                div.setAttribute('class', 'alert alert-success float-center');
                div.setAttribute('id', 'message_flash');
                div.setAttribute('style', 'top: '+ (window.scrollY+200) +'px; text-align: center; width: 200px; margin: 0 auto;');
                div.innerHTML += "Le lieu à été ajouté!";
                document.body.prepend(div);

                // Retire le message après un certain temps
                return new Promise((resolve)=>{
                    setTimeout(()=>{
                        resolve();
                    }, 3000)
                }).then(()=>{
                    document.body.removeChild(document.getElementById('message_flash'));
                })
            });
        }
    }
}