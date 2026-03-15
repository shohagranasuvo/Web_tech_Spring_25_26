
const unitPrice = 1000;
const days = 30;
let quantityInput = document.getElementById("quantity");
let totalPriceInput = document.getElementById("totalPrice");
function calculateTotal(){
    let quantity = Number(quantityInput.value);
    if(quantity < 0)
    {
        alert("Quantity cannot be negative!");
        quantity = 0;
        quantityInput.value = 0;
    }
    let total = unitPrice * quantity * days;
   totalPriceInput.value=total;
    if(total > 1000)
    {
        alert(" Congratulations! You are eligible for a Gift Coupon!");
    }
};
quantityInput.addEventListener("input", calculateTotal);