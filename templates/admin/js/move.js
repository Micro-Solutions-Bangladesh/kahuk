function moveup(id) {
    var cat = document.getElementById('cat' + id);
    var prevCat = cat.previousElementSibling;

    if (prevCat) { // Check if previous element exist
        var prevCatId = prevCat.id.substr(3);
        var parentdoc = cat.parentNode;

        parentdoc.removeChild(cat);
        parentdoc.insertBefore(cat, prevCat);

        // console.log('up2: up' + prevCatId);

        var up1 = document.getElementById('up' + id);
        var up2 = document.getElementById('up' + prevCatId);
        var down1 = document.getElementById('down' + id);
        var down2 = document.getElementById('down' + prevCatId);

        var i = up1.style.display;
        up1.style.display = up2.style.display;
        up2.style.display = i;

        i = down1.style.display;
        down1.style.display = down2.style.display;
        down2.style.display = i;

        if (XMLHttpRequestObject) {
            XMLHttpRequestObject.open("GET", "?action=move_above&id_to_move=" + id + "&moveabove_id=" + prevCatId, true);
            XMLHttpRequestObject.send(null);
        }
    }
}

function movedown(id) {
    var cat = document.getElementById('cat' + id);
    var next = cat.nextElementSibling;

    if (next) {
        var prevCatId = next.id.substr(3);
        moveup(prevCatId);
    }
}