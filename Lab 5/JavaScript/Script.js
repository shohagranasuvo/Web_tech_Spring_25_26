console.log("Connected");
function showTotalMark(){
    let firstnametext =document.getElementById("InputFirstName").value ;
    let Lastname =document.getElementById("InputLastName").value ;
     let email =document.getElementById("InputEmail").value ;
     let phone =document.getElementById("InputPhone").value ;
    let check =1 ;
    if(firstnametext=="")
    {
        document.getElementById("firstnamewrong").innerHTML ="First Name Empty" ;
      check=0 ;
       
    }
    if(Lastname=="")
    {
        document.getElementById("lasttNameWrong").innerHTML ="Last Name Empty" ;
       check = 0 ;
    }
     if(email=="")
    {
        document.getElementById("emailwrong").innerHTML ="Email Empty" ;
        check = 0 ;
    }
    if(phone=="")
    {
         document.getElementById("phoneWrong").innerHTML ="Phone number Empty" ;
        check = 0 ;

    }
    if(check==0)
    {
        return false  ;
    }
    else{
        console.log("First name" ,firstnametext);
        console.log("Last Name" ,Lastname)
    }

   
    return false ;
   
}
