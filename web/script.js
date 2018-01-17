console.log('youpi');

$(document).ready(function () {
    $( "#manager_categorie" ).change(function() {
        if($(this)[0].value > 0)
        {
            var path = "galerie?categorie="+$(this)[0].value;
            window.location.href = path;
        }
        console.log(path);
    });
});