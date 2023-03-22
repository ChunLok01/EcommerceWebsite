function opencart() {
    document.getElementById("masking").classList.remove("hidediv");
    document.getElementById("content").classList.add("contentflow");
    document.getElementById("modelex").classList.remove("hidediv");
    document.getElementById("modelex").classList.add("opencart");
    document.getElementById("mask").classList.remove("hidediv");
    document.getElementById("mask").classList.add("maskpos");
}
function closecart() {
    document.getElementById("modelex").classList.remove("opencart");
    document.getElementById("modelex").classList.add("closecart");
    setTimeout(
        function () {
            document.getElementById("modelex").classList.remove("closecart");
            document.getElementById("modelex").classList.add("hidediv");
            document.getElementById("mask").classList.remove("maskpos");
            document.getElementById("mask").classList.add("hidediv");
            document.getElementById("content").classList.remove("contentflow");
            document.getElementById("masking").classList.add("hidediv");
        }, 450
    );
}