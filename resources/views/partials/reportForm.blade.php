<!-- Report form -->
<div id="reportFormModal" class = "w-full h-full fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden z-20">
    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
        <header class = "grid grid-cols-3 justify-center w-full max-w-full mb-4">
            <button onclick = "toggleReportForm(null,null)" class="col-start-1 col-span-1 justify-self-start">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h2 class=" col-start-2  text-2xl self-center font-bold text-nowrap">Report Content</h2>  
        </header>
        <form id="reportForm">
            <label for = "reason" class = "font-medium">Reason</label>
            <textarea class = "shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900"  id = "reason" name = "reason"></textarea>
            <input type="hidden"  name = "comment" id="reportComment">
            <input type="hidden" name = "post" id = "reportPost">
            <button class = "flex gap-2 px-6 py-2 bg-sky-700 text-white font-semibold rounded-xl hover:bg-sky-800 text-sm" id="sendReport">Send</button>
         </form>
        
    </div>
</div>