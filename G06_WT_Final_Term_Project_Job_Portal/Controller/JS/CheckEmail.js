function CheckEmail() {
    let email= document.getElementById("emailfield").value;
    let xhttp= new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let response = JSON.parse(this.responseText);
            let el = document.getElementById("emailresponse");
            if (response.available) {
                el.style.color = "green";
                el.innerHTML = response.message;
            } else {
                el.style.color = "red";
                el.innerHTML = response.message;
            }
        }
    };

    xhttp.open("POST", "../Controller/CheckEmail.php", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("email=" + encodeURIComponent(email));
}

