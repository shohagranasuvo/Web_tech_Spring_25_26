console.log("Connected");
function showTotalMark(){
    let num1 =Number(document.getElementById("markweb").value) ;
    let num2 =Number(document.getElementById("markjava").value) ;
    let total =Number(num1+num2) ;
    console.log("Total mark : " ,total) ;
    document.getElementById("totalmark").innerHTML = total ;
    return false ;
   
}
