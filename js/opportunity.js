document.addEventListener("DOMContentLoaded", function () {
  // Form elements
  const form = document.getElementById("opportunity-form");
  const formSteps = document.querySelectorAll(".form-step");
  const progressBar = document.getElementById("progress-bar");
  const stepIndicator = document.getElementById("step-indicator");
  const nextBtn = document.getElementById("next-btn");
  const backBtn = document.getElementById("back-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const postBtn = document.getElementById("post-btn");
  const mediaUpload = document.getElementById("media_upload");
  const mediaArea = document.querySelector(
    ".border.border-dashed.border-blue-300"
  );

  let currentStep = 1;
  const totalSteps = 3;

  // Update progress bar
  function updateProgressBar() {
    const progress = (currentStep / totalSteps) * 100;
    progressBar.style.width = `${progress}%`;
    stepIndicator.textContent = `${currentStep}/${totalSteps}`;

    backBtn.classList.toggle("hidden", currentStep === 1);
    nextBtn.classList.toggle("hidden", currentStep === totalSteps);
    postBtn.classList.toggle("hidden", currentStep !== totalSteps);
  }

  // Show current step
  function showStep(stepNumber) {
    formSteps.forEach((step, index) => {
      step.classList.toggle("hidden", index !== stepNumber - 1);
    });

    if (stepNumber === 4) {
      document.getElementById("review-step").classList.remove("hidden");
      document.getElementById("step-3").classList.add("hidden");
      populateReviewData();
    }

    updateProgressBar();
  }

  // Validate form inputs for current step
  function validateStep(stepNumber) {
    if (stepNumber === 1) {
      const title = document.getElementById("title").value;
      const company = document.getElementById("company").value;
      const location = document.querySelector('input[name="location"]:checked');
      const listingType = document.querySelector(".listing-type-btn.bg-navy");
      if (!title || !company || !location || !listingType) {
        alert("Please fill all required fields in Step 1.");
        return false;
      }
    } else if (stepNumber === 2) {
      const budgetAmount = document.getElementById("budget_amount").value;
      const budgetCycle = document.getElementById("budget_cycle").value;
      const budgetCurrency = document.getElementById("budget_currency").value;
      const jobTiming = document.querySelector(".job-timing-btn.bg-navy");
      const jobShift = document.querySelector(".job-shift-btn.bg-navy");
      if (
        !budgetAmount ||
        !budgetCycle ||
        !budgetCurrency ||
        !jobTiming ||
        !jobShift
      ) {
        alert("Please fill all required fields in Step 2.");
        return false;
      }
    } else if (stepNumber === 3) {
      const tags = document.querySelectorAll(".tag-btn.bg-navy");
      if (tags.length === 0) {
        alert("Please select at least one tag in Step 3.");
        return false;
      }
    }
    return true;
  }

  // Populate review data
  function populateReviewData() {
    document.getElementById("review-title").textContent =
      document.getElementById("title").value || "Opportunity Title";
    document.getElementById("review-company").textContent =
      document.getElementById("company").value || "Company name";

    const locationRadios = document.querySelectorAll('input[name="location"]');
    let selectedLocation = "On-site";
    locationRadios.forEach((radio) => {
      if (radio.checked) {
        selectedLocation =
          radio.value.charAt(0).toUpperCase() + radio.value.slice(1);
      }
    });
    document.getElementById("review-location-type").textContent =
      `· ${selectedLocation}`;

    const budgetAmount = document.getElementById("budget_amount").value;
    const budgetCurrency = document
      .getElementById("budget_currency")
      .value.toUpperCase();
    document.getElementById("review-salary").textContent =
      `· ${budgetAmount} ${budgetCurrency}`;

    const budgetCycle = document.getElementById("budget_cycle").value;
    document.getElementById("review-cycle").textContent =
      `· ${budgetCycle.charAt(0).toUpperCase() + budgetCycle.slice(1)}`;

    const jobTiming = document.querySelector(".job-timing-btn.bg-navy");
    document.getElementById("review-hours").textContent =
      `· ${jobTiming ? jobTiming.dataset.timing : "Not specified"} hrs/wk`;

    const jobShift = document.querySelector(".job-shift-btn.bg-navy");
    document.getElementById("review-shift").textContent =
      `· ${jobShift ? jobShift.dataset.shift.charAt(0).toUpperCase() + jobShift.dataset.shift.slice(1) : "Not specified"}`;

    document.getElementById("review-description").textContent =
      document.getElementById("description").value ||
      "Lorem ipsum dolor sit amet consectetur.";

    const tagsContainer = document.querySelector(".flex.flex-wrap.gap-2.mt-4");
    tagsContainer.innerHTML = "";
    const selectedTags = document.querySelectorAll(".tag-btn.bg-navy");
    selectedTags.forEach((tag, index) => {
      if (index < 6) {
        const span = document.createElement("span");
        span.className =
          "px-4 py-1 border border-[rgba(2,53,100,1)] rounded-full text-sm";
        span.textContent = tag.textContent.trim().replace("+", "");
        tagsContainer.appendChild(span);
      }
    });
    if (selectedTags.length > 6) {
      const moreSpan = document.createElement("span");
      moreSpan.className = "text-sm";
      moreSpan.textContent = `+${selectedTags.length - 6}`;
      tagsContainer.appendChild(moreSpan);
    }
  }

  // Handle next button click
  nextBtn.addEventListener("click", function () {
    if (validateStep(currentStep)) {
      if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
      } else if (currentStep === totalSteps) {
        currentStep = 4; // Review step
        showStep(currentStep);
      }
    }
  });

  // Handle back button click
  backBtn.addEventListener("click", function () {
    if (currentStep > 1) {
      currentStep--;
      showStep(currentStep);
    }
  });

  // Handle cancel button click
  cancelBtn.addEventListener("click", function () {
    if (confirm("Are you sure you want to cancel? All data will be lost.")) {
      form.reset();
      currentStep = 1;
      showStep(currentStep);
      document
        .querySelectorAll(
          ".listing-type-btn, .job-timing-btn, .job-shift-btn, .tag-btn"
        )
        .forEach((btn) => {
          btn.classList.remove("bg-navy", "text-white");
        });
    }
  });

  // Handle post button click (submit to backend)
  postBtn.addEventListener("click", function (e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("title", document.getElementById("title").value);
    formData.append(
      "listing_type",
      document.querySelector(".listing-type-btn.bg-navy")?.dataset.type || ""
    );
    formData.append("company", document.getElementById("company").value);
    formData.append(
      "location",
      document.querySelector('input[name="location"]:checked')?.value || ""
    );
    formData.append("start_date", document.getElementById("start_date").value);
    formData.append("end_date", document.getElementById("end_date").value);
    formData.append(
      "budget_amount",
      document.getElementById("budget_amount").value
    );
    formData.append(
      "budget_cycle",
      document.getElementById("budget_cycle").value
    );
    formData.append(
      "budget_currency",
      document.getElementById("budget_currency").value
    );
    formData.append(
      "job_timing",
      document.querySelector(".job-timing-btn.bg-navy")?.dataset.timing || ""
    );
    formData.append(
      "job_shift",
      document.querySelector(".job-shift-btn.bg-navy")?.dataset.shift || ""
    );
    formData.append(
      "description",
      document.getElementById("description").value
    );
    const tags = Array.from(document.querySelectorAll(".tag-btn.bg-navy")).map(
      (tag) => tag.textContent.trim().replace("+", "")
    );
    formData.append("tags", JSON.stringify(tags));
    if (mediaUpload.files[0]) {
      formData.append("media", mediaUpload.files[0]);
    }

    fetch("./backend/save_opportunity.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        // Check if the response is JSON
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
          throw new Error(
            "Server did not return JSON. Received: " + contentType
          );
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          alert("Your opportunity has been posted!");
          form.reset();
          currentStep = 1;
          showStep(currentStep);
          document
            .querySelectorAll(
              ".listing-type-btn, .job-timing-btn, .job-shift-btn, .tag-btn"
            )
            .forEach((btn) => {
              btn.classList.remove("bg-navy", "text-white");
            });
        } else {
          alert("Error posting opportunity: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert(
          "An error occurred while posting the opportunity: " + error.message
        );
      });
  });

  // Handle button selections
  const listingButtons = document.querySelectorAll(".listing-type-btn");
  listingButtons.forEach((button) => {
    button.addEventListener("click", function () {
      listingButtons.forEach((btn) =>
        btn.classList.remove("bg-navy", "text-white")
      );
      this.classList.add("bg-navy", "text-white");
    });
  });

  const jobTimingButtons = document.querySelectorAll(".job-timing-btn");
  jobTimingButtons.forEach((button) => {
    button.addEventListener("click", function () {
      jobTimingButtons.forEach((btn) =>
        btn.classList.remove("bg-navy", "text-white")
      );
      this.classList.add("bg-navy", "text-white");
    });
  });

  const jobShiftButtons = document.querySelectorAll(".job-shift-btn");
  jobShiftButtons.forEach((button) => {
    button.addEventListener("click", function () {
      jobShiftButtons.forEach((btn) =>
        btn.classList.remove("bg-navy", "text-white")
      );
      this.classList.add("bg-navy", "text-white");
    });
  });

  const tagButtons = document.querySelectorAll(".tag-btn");
  tagButtons.forEach((button) => {
    button.addEventListener("click", function () {
      this.classList.toggle("bg-navy");
      this.classList.toggle("text-white");
    });
  });

  // Handle media upload
  mediaArea.addEventListener("click", () => mediaUpload.click());

  mediaArea.addEventListener("dragover", (e) => {
    e.preventDefault();
    mediaArea.classList.add("bg-blue-50");
  });

  mediaArea.addEventListener("dragleave", () => {
    mediaArea.classList.remove("bg-blue-50");
  });

  mediaArea.addEventListener("drop", (e) => {
    e.preventDefault();
    mediaArea.classList.remove("bg-blue-50");
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      mediaUpload.files = files;
      validateFile(files[0]);
    }
  });

  mediaUpload.addEventListener("change", () => {
    if (mediaUpload.files.length > 0) {
      validateFile(mediaUpload.files[0]);
    }
  });

  function validateFile(file) {
    const allowedFormats = [
      "image/jpeg",
      "image/png",
      "application/pdf",
      "image/svg+xml",
      "video/mp4",
    ];
    const maxSize = 25 * 1024 * 1024; // 25MB
    if (!allowedFormats.includes(file.type)) {
      alert(
        "Unsupported file format. Please upload JPG, PNG, PDF, SVG, or MP4."
      );
      mediaUpload.value = "";
    } else if (file.size > maxSize) {
      alert("File size exceeds 25MB limit.");
      mediaUpload.value = "";
    } else {
      mediaArea.querySelector("p").textContent = `File selected: ${file.name}`;
    }
  }

  // Initialize first step
  showStep(currentStep);
});
