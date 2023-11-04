<style>
    /* CSS for Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
}

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 60%;
}

.close-modal {
    color: #888;
    float: right;
    font-size: 30px;
    cursor: pointer;
}
.blue-link {
        color: blue;
        text-decoration: underline; /* Optionally, add an underline to make it look like a typical link */
    }

/* Add more styling as needed */


</style>
  <footer class="py-10 bg-gray-200 text-gray-900">
    <div class="container px-6 mx-auto space-y-6 divide-y divide-gray-400 md:space-y-12 divide-opacity-50">
        <div class="grid justify-center lg:justify-between">
            <div class="flex flex-col self-center text-sm text-center md:block lg:col-start-1 md:space-x-6">
                <span>Copy right © 2023 by Follow the Leader</span>
                <a rel="noopener noreferrer" href="#" class="open-modal" data-target="privacy-modal">
                    <a href="{{ route('terms.privacy') }}" class="blue-link">Privacy policy</a>

                </a>
                <a rel="noopener noreferrer" href="#" class="open-modal" data-target="terms-modal">
                    <a href="{{ route('terms.show') }}" class="blue-link">Terms and Conditions</a>

                </a>
            </div>
        </div>
    </div>
</footer>
{{--
<div id="privacy-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" data-target="privacy-modal">&times;</span>
        <!-- Privacy Policy content goes here -->
    </div>
</div>

<div id="terms-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" data-target="terms-modal">&times;</span>
        <!-- Terms of Service content goes here -->
    </div>
</div>

<script src="script.js"></script>
<script>
const modalButtons = document.querySelectorAll(".open-modal");
modalButtons.forEach(button => {
    button.addEventListener("click", function() {
        const targetId = this.getAttribute("data-target");
        const modal = document.getElementById(targetId);
        modal.style.display = "block";
    });
});

const closeButtons = document.querySelectorAll(".close-modal");
closeButtons.forEach(button => {
    button.addEventListener("click", function() {
        const targetId = this.getAttribute("data-target");
        const modal = document.getElementById(targetId);
        modal.style.display = "none";
    });
});
</script> --}}
