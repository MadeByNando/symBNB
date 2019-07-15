// Au clic sur le bouton #add-image
$('#add-image').click(function(){

    // Je récupère le numéro des futurs champs que je vais créer
    const index = +$("#widgets-counter").val();

    // Je récupère le prototype des entrées (le g de la règle regex indique qu'on remplace plusieurs fois /__name__/)
    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g, index);

    // J'injecte ce code au sein de la div
    $("#annonce_images").append(tmpl);

    // On incrémente la valeur du compteur de widgets
    $("#widgets-counter").val(index + 1);

    // Je gère le bouton supprimer
    handleDeleteButtons();

});

// Gestion des boutons de suppression
function handleDeleteButtons(){

    // Au clic sur un bouton avec l'attribut data-action="delete"
    $('button[data-action="delete"]').click(function(){

        // On récupère la cible contenu dans l'attribut data-target
        const target = this.dataset.target;

        // On supprime la cible
        $(target).remove();
    });
}

// Calculer l'index en prenant en compte le nombre d'images déjà existantes
function updateCounter(){
    const count = +$('#annonce_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();

// Je gère le bouton supprimer
handleDeleteButtons(); 