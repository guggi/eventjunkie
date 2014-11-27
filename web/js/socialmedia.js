var counter = 1;
var limit = 3;
function addInput(divName){
    if (counter == limit)  {
        alert("You have reached the limit of adding " + counter + " inputs");
    }
    else {
        var newdiv = document.createElement('div');
        newdiv.innerHTML = "Entry " + (counter + 1) + "<?= ($form->field($socialMediaModel, '[]url')->textInput(['maxlength' => 500])) ?>";
        document.getElementById(divName).appendChild(newdiv);
        counter++;
    }
}