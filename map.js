var mymap = null;

var parking_velo_list=new Array();
var parking_list=new Array();
var restaurants_list=new Array();

var user;

//Initialize map
function initmap(){
    mymap=L.map('mapid').setView([48.6924654452072, 6.18340055389445], 13);
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 18,
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
            '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        id: 'mapbox.streets'
    }).addTo(mymap);    
}

//EventListener
$('#park_velo').change(veloParkManager);
$('#parking').change(parkingManager);
$('#restaurant').change(restaurantManager);

//Manager
function veloParkManager(){
    if($(this).is(':checked')){
        parking_velo_list.forEach(element => {
            mymap.addLayer(element);
        });
    }else{        
        parking_velo_list.forEach(element => {
            mymap.removeLayer(element);
        });
    }
}

function parkingManager(){
    if($(this).is(':checked')){
        parking_list.forEach(element => {
            mymap.addLayer(element);
        });
    }else{        
        parking_list.forEach(element => {
            mymap.removeLayer(element);
        });
    }
}

function restaurantManager(){
    if($(this).is(':checked')){
        restaurants_list.forEach(element => {
            mymap.addLayer(element);
        });
    }else{        
        restaurants_list.forEach(element => {
            mymap.removeLayer(element);
        });
    }
}

//Icons
var greenIcon = L.icon({
    iconUrl: 'img/green-marker-icon.png',
});
var redIcon = L.icon({
    iconUrl: 'img/red-marker-icon.png',
});

//Map function
function removeMarker(marker){
    mymap.removeLayer(marker);
}

function addMarkerParkingVelo(name,lat,lon,avail,free,objectId){
    var new_marker = L.marker([lat, lon]).addTo(mymap);
    new_marker.bindPopup("<b>"+name+"</b><p>Disponible : "+avail+"</p><p>Point de rattachement : "+free+"</p><a class='detail_btn' onclick='getDetail(\""+objectId+"\",\"parking_velo\");'>Détails sur ce lieu</a>");
    parking_velo_list.push(new_marker);    
}

function addMarkerParking(name,lat,lon,capacity,places,isInit,objectId){
    var new_marker = L.marker([lat, lon],{icon:greenIcon}).addTo(mymap);    
    new_marker.bindPopup("<b>"+name+"</b><p>Places disponibles : "+places+"/"+capacity+"</p><a class='detail_btn' onclick='getDetail(\""+objectId+"\",\"parking\");'>Détails sur ce lieu</a>");    
    parking_list.push(new_marker);
    if(isInit){
        mymap.removeLayer(new_marker);        
    }
}

function addMarkerRestaurants(name,lat,lon,telephone,addresse,isInit,objectId){
    var new_marker = L.marker([lat, lon],{icon:redIcon}).addTo(mymap);    
    new_marker.bindPopup("<b>"+name+"</b><p>N° de téléphone : "+telephone+"</p><p>Addresse : "+addresse+"</p><a class='detail_btn' onclick='getDetail(\""+objectId+"\",\"restaurants\");'>Détails sur ce lieu</a>");    
    restaurants_list.push(new_marker);
    if(isInit){
        mymap.removeLayer(new_marker);        
    }
}

function getNearestParking(lat,lon) {
    //Only get markers we want
    resto = $('#restaurant');
    parking = $('#parking');
    park_velo = $('#park_velo');

    if(!resto.is(':checked')){
        resto.trigger("click");
    }
    if(!parking.is(':checked')){
        parking.trigger("click");
    }
    
    var minDist = 1000;
    var nearest_marker=null;
      
    for(var i=0;i<parking_list.length;i++){
    marker = parking_list[i];    
    markerDist = measure(marker["_latlng"].lat,marker["_latlng"].lng,lat,lon);    
      if (markerDist < minDist) {
        minDist = markerDist;
        nearest_marker = marker;
      }
    }    
    nearest_marker.openPopup();

    $(".detail_btn").trigger("click");
}

function measure(lat1, lon1, lat2, lon2){  // generally used geo measurement function
    var R = 6378.137; // Radius of earth in KM
    var dLat = lat2 * Math.PI / 180 - lat1 * Math.PI / 180;
    var dLon = lon2 * Math.PI / 180 - lon1 * Math.PI / 180;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
    Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = R * c;
    return d * 1000; // meters
}

function getDetail(objectId,table){
    $.getJSON("./getDetail.php?objId="+objectId+"&table="+table,function(data){
                          
            details = $('#detail_info');            
            details.html('<h3 id="'+objectId+'" class="'+table+'">'+data.name+'</h3>');
            if(table=="restaurants"){
                details.append('<li><button onclick="getNearestParking('+data.lat+','+data.lng+')">Parking le plus proche</button></li>'); 
            }            
            Object.keys(data).forEach(key => {
                if(["name","lng","lat","_id"].indexOf(key)<0){
                    if(key=="site"){
                        details.append('<li>'+key+' : <a target="_blank" href="'+data[key]+'">'+data[key]+'</a></li>');                          
                    }else if(key=="horaires"){       
                        var content = "";                 
                        content+="<li>"+key+" : <ul>";
                        Object.keys(data.horaires).forEach(day => {                            
                            content+="<li>"+day + " : "
                                        +"<ul>"
                                            +"<li>"+data.horaires[day].periode.debut+"-"+data.horaires[day].periode.fin+"</li>"
                                        +"</ul>"
                                    +"</li>";
                        });
                        content+="</ul></li>";                        
                        details.append(content);
                    }else if(key=="comments"){                          
                        var content = "";
                        content+="<li>"+key+" : <ul class='comment_list'>";
                        if(data.comments.length>0){
                            Object.keys(data[key]).forEach(id=>{
                                comment=data[key][id];
                                content+='<li><b>'+comment.from+'</b><p>'+comment.message+'</p></li>';
                            });
                        }else{
                            content+='<li id="no_comment"><b>Aucun commentaire pour ce lieu</b></li>';
                        }
                        if(user!=null){
                            content+="<li id='add_comment'><b>Ajouter un commentaire</b><input id='edtcomment'/><button onclick='createCommentaire()'>Envoyer</button></li>"
                        }
                        content+="</ul></li>";                            
                        
                        details.append(content);                                                              
                    }else details.append('<li>'+key+' : '+data[key]+'</li>');      
                }
            });
        });
    }                                

function createCommentaire(){  
    message=$('#edtcomment').val();
    objId=$('#detail_info h3')[0].id;
    table=$('#detail_info h3')[0].classList[0];
        
    $.ajax({
        url:"./createComment.php?message="+message+"&table="+table+"&objId="+objId+"&user="+user,
        success:function(data){
            $('#edtcomment').val("");
            $('#add_comment').before('<li><b>'+user+'</b><p>'+message+'</p>');  
            $('#no_comment').remove();          
        }        
    });
}

function addToConsole(message){
    console.log(message);
}

function tryToConnect(action){   
    if($('#edtlogin').val().trim() != "" && $('#edtpsw').val().trim() != "" ){
        var url = null;
        if(action=="createAccount"){
            url="createAccount.php?login="+$('#edtlogin').val()+"&psw="+$('#edtpsw').val();
        }else{
            url="connection.php?login="+$('#edtlogin').val()+"&psw="+$('#edtpsw').val();
        }
        
        $.getJSON(url,function(data){        
            if(data.user!=null){
                user=data.user;
                $("#connexion").html('<h1>Bienvenue '+ user+'</h1>');
                $("#connexion").append('<button onclick="disconnection()">Déconnexion</button>');
                $('.comment_list').append("<li id='add_comment'><b>Ajouter un commentaire</b><input id='edtcomment'/><button onclick='createCommentaire()'>Envoyer</button></li>")
            }else {            
                user=null;
                if(action=="createAccount"){
                    alert('Un utilisateur a le même login');
                }else{
                    alert('Login ou mot de passe incorrect');
                }
            }
        });
    }else{
        alert("Veuillez remplir tous les champs pour pouvoir vous identifier");
    }
}

function disconnection(){
    $.ajax({
        url:'disconnection.php',
        success:function(data){      
            user=null;  
            $('#connexion').html('<h2>Connectez vous ou créez un compte pour pouvoir poster des commentaires</h2><input id="edtlogin"/><input type="password" id="edtpsw"/><div class="flex-row"><button onclick="tryToConnect()">Connexion</button><button onclick="tryToConnect(\'createAccount\')">Créer mon compte</button></div>')
            $('#add_comment').remove();
        }
    });
}