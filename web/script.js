console.log('youpi!');

$(document).ready(function () {
    $( "#manager_categorie" ).change(function() {
        if($(this)[0].value > 0)
        {
            var path = "galerie?categorie="+$(this)[0].value;
            window.location.href = path;
        }
        console.log(path);
    });

    $(document).on('click', '.changeEtatGalerie', function (e) {

        e.preventDefault();

        var td = $(this);
        var url = td.attr('data-url');
        td.html('');

        $.ajax(url,{
            dataType : "JSON",
            cache:false
        })
        .done(function(data){
            console.log(data.state);
            if(data.state){
                console.log('on passe a 1');
                td.html('1');
            }else{
                console.log('on passe a 0');
                td.html('0');
            }
        })
        .fail(function(){
            alert('Erreur Ajax');
        });

    });

});

