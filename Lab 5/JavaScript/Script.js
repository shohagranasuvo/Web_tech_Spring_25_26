console.log("Connected");
function showTotalMark(){
    let firstnametext =document.getElementById("InputFirstName").value ;
    let Lastname =document.getElementById("InputLastName").value ;
     let email =document.getElementById("InputEmail").value ;
     let phone =document.getElementById("InputPhone").value ;
     let info =document.getElementById("infotext").value ;
    let check =1 ;
    if(firstnametext=="")
    {
        document.getElementById("firstnamewrong").innerHTML ="First Name Empty" ;
      check=0 ;
       
    }
    else
    {
         document.getElementById("firstnamewrong").innerHTML ="" ;

    }
    if(Lastname=="")
    {
        document.getElementById("lasttNameWrong").innerHTML ="Last Name Empty" ;
       check = 0 ;
    }
    else
    {
        document.getElementById("lasttNameWrong").innerHTML ="" ;
    }
     if(email=="")
    {
        document.getElementById("emailwrong").innerHTML ="Email Empty" ;
        check = 0 ;
    }
    else
    {
        document.getElementById("emailwrong").innerHTML ="" ;

    }
    if(phone=="")
    {
         document.getElementById("phoneWrong").innerHTML ="Phone number Empty" ;
        check = 0 ;

    }
    else
    {
         document.getElementById("phoneWrong").innerHTML ="" ;

    }
    if(info=="")
    {
        document.getElementById("wrongifo").innerHTML="Info empty" ;
       check = 0 ;
    }
    else
    {
        document.getElementById("wrongifo").innerHTML="" ;

    }

    if(check==0)
    {
        return false  ;
    }
    else{
        console.log("First name" ,firstnametext);
        console.log("Last Name" ,Lastname) ;
        console.log("Email" ,email);
        console.log("Phone" ,phone);
        console.log("Message" ,info);
    }

   
    return false ;
   
}
