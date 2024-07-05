document.addEventListener('DOMContentLoaded', function() {
    function showModal() {
      // Create modal backdrop
      const backdrop = document.createElement('div');
      backdrop.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full';
      backdrop.id = 'modal-backdrop';
  
      // Create modal content
      const modal = document.createElement('div');
      modal.className = 'relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white';
  
      // Modal header
      const header = document.createElement('div');
      header.className = 'mt-3 text-center';
      header.innerHTML = '<h3 class="text-lg leading-6 font-medium text-gray-900">Enter Your Details</h3>';
  
      // Create form
      const form = document.createElement('form');
      form.className = 'mt-2 space-y-4';
  
      // Name input
      const nameInput = document.createElement('input');
      nameInput.type = 'text';
      nameInput.placeholder = 'Enter your name';
      nameInput.className = 'mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50';
  
      // Email input
      const emailInput = document.createElement('input');
      emailInput.type = 'email';
      emailInput.placeholder = 'Enter your email';
      emailInput.className = 'mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50';
  
      // Submit button
      const submitButton = document.createElement('button');
      submitButton.type = 'submit';
      submitButton.textContent = 'Submit';
      submitButton.className = 'w-full px-4 py-2 text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-md';
  
      // Close button
      const closeButton = document.createElement('button');
      closeButton.textContent = 'Close';
      closeButton.className = 'absolute top-0 right-0 mt-4 mr-4 text-gray-500 hover:text-gray-600';
      closeButton.onclick = function() {
        document.body.removeChild(backdrop);
      };
  
      // Append elements
      form.appendChild(nameInput);
      form.appendChild(emailInput);
      form.appendChild(submitButton);
      modal.appendChild(closeButton);
      modal.appendChild(header);
      modal.appendChild(form);
      backdrop.appendChild(modal);
  
      // Add modal to the document body
      document.body.appendChild(backdrop);
    }
  
    // Create and add trigger button
    const triggerButton = document.createElement('button');
    triggerButton.textContent = 'Open Form';
    triggerButton.className = 'px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600';
    triggerButton.addEventListener('click', showModal);
    document.body.appendChild(triggerButton);
  });