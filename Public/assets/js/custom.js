var test = null;
let isVerification = false;
let verificationPng = null;
let istoVerify = false;

let captureBtn = $("#capture");
let enrollBtn = $("#enroll");
let verifyBtn = $("#verify");

let scannerImg = $(".finger-image-scan");
let fingerContainer = $(".fingerprint_inner_scanned");
let handChildren = $(".print-container");
let printPreview = $(".fingerprint_preview");

let printReader = $("#print_readers");
let bioHand = $(".biometric-title");
let clearBtn = $(".clear");

let currentFingerName = "";
let isCaptured = {
  left: "",
  right: "",
};

let currentHand = 0;
let currentFinger = 0;

enrollBtn.prop("disabled", true);
captureBtn.prop("disabled", true);

var state = document.getElementById("content-capture");

var myVal = ""; // Drop down selected value of reader
var disabled = true;
var startEnroll = false;

var currentFormat = Fingerprint.SampleFormat.PngImage;
var deviceTechn = {
  0: "Unknown",
  1: "Optical",
  2: "Capacitive",
  3: "Thermal",
  4: "Pressure",
};
var deviceModality = {
  0: "Unknown",
  1: "Swipe",
  2: "Area",
  3: "AreaMultifinger",
};

var deviceUidType = {
  0: "Persistent",
  1: "Volatile",
};

// Initiate javascript sdk===============================
// ==========================================================
var FingerprintSdkTest = (function () {
  function FingerprintSdkTest() {
    var _instance = this;
    this.operationToRestart = null;
    this.acquisitionStarted = false;
    this.sdk = new Fingerprint.WebApi();
    this.sdk.onDeviceConnected = function (e) {
      // Detects if the deveice is connected for which acquisition started
      bruiz.detected();
    };
    this.sdk.onDeviceDisconnected = function (e) {
      // Detects if device gets disconnected - provides deviceUid of disconnected device
      bruiz.message();
    };
    this.sdk.onCommunicationFailed = function (e) {
      // Detects if there is a failure in communicating with U.R.U web SDK
      bruiz.message("error", "Failed", "Cannot communicate to the reader");
    };
    this.sdk.onSamplesAcquired = function (s) {
      // Sample acquired event triggers this function
      sampleAcquired(s);
    };
    this.sdk.onQualityReported = function (e) {
      // Quality of sample aquired - Function triggered on every sample acquired
      console.log(Fingerprint.QualityCode[e.quality]);
    };
  }

  FingerprintSdkTest.prototype.startCapture = function () {
    if (this.acquisitionStarted)
      // Monitoring if already started capturing
      return;
    var _instance = this;
    this.operationToRestart = this.startCapture;
    this.sdk.startAcquisition(currentFormat, myVal).then(
      function () {
        _instance.acquisitionStarted = true;
        //Disabling start once started
        isCapturing();
      },
      function (error) {
        showMessage(error.message);
      }
    );
  };
  FingerprintSdkTest.prototype.stopCapture = function () {
    if (!this.acquisitionStarted)
      //Monitor if already stopped capturing
      return;
    var _instance = this;
    this.sdk.stopAcquisition().then(
      function () {
        _instance.acquisitionStarted = false;

        //Disabling stop once stoped
        setStopCapture();
      },
      function (error) {
        bruiz.message("error", "", error.message);
      }
    );
  };

  FingerprintSdkTest.prototype.getInfo = function () {
    var _instance = this;
    return this.sdk.enumerateDevices();
  };

  FingerprintSdkTest.prototype.getDeviceInfoWithID = function (uid) {
    var _instance = this;
    return this.sdk.getDeviceInfo(uid);
  };

  return FingerprintSdkTest;
})();

// Initiate fingerprint scanner on load
window.onload = function () {
  loadFingerPrintScanner();
  //To populate readers for drop down selection
  // enableDisableScanQualityDiv("content-reader"); // To enable disable scan quality div
  // disableEnableExport(true);
};
function loadFingerPrintScanner() {
  test = new FingerprintSdkTest();
  listScanners();
}

// ***********************************************************
// Ge the list of availabe scanners
// ***********************************************************
function listScanners() {
  myVal = "";
  let readers = test.getInfo();
  readers.then(
    (res) => {
      let printReaders = document.getElementById("print_readers");
      for (let i = 0; i < res.length; i++) {
        let options = document.createElement("option");
        options.value = res[i];
        options.text = res[i];
        printReaders.add(options);
      }
      //Check if readers are available get count and  provide user information if no reader available,
      //if only one reader available then select the reader by default and sennd user to capture tab
      checkReaders({ res, reader: printReaders });
    },
    (err) => {
      bruiz.message("error", "Error", err.message);
    }
  );
  console.log(readers);
}

// ****************************************************
// check the available scanners =======================
// ****************************************************
function checkReaders({ res, reader }) {
  if (res.length == 0) {
    bruiz.message("error", "", "No Reader Detected, Please connect a reader");
  } else if (res.length > 0) {
    captureBtn.prop("disabled", false);
    reader.selectedIndex = 1;
    myVal = res[0];
    disableEnable(); // Disabling enabling buttons - if reader not selected
    // console.log(myVal);
  }
}

// **********************************
// Start Capturing *****************
// **********************************
function startCapture() {
  currentFormat = Fingerprint.SampleFormat.PngImage;
  test.startCapture();
}
// **********************************
// Stop Capturing *****************
// **********************************
function stopCapture() {
  test.stopCapture();
}

function disableEnable() {
  if (myVal != "") {
    disabled = false;
    captureBtn.prop("disabled", false);
    // disableEnableStartStop();
  } else {
    disabled = true;
    captureBtn.prop("disabled", true);
    enrollBtn.prop("disabled", true);
    verifyBtn.prop("disabled", true);
    bruiz.message("error", "", "Please select a reader");
    onStop();
  }
}
function setStopCapture() {
  enrollBtn.prop("disabled", false);
  verifyBtn.prop("disabled", false);
  if (!istoVerify) {
    $(scannerImg).attr("src", "/Public/assets/img/finger.png");
  }
  console.log("Capturing is now stopped");
  // $(clearBtn).fadeIn().prop('disabled', false)
}

captureBtn.on("click", function () {
  let ver = $(this).data("ver");
  if (ver) isVerification = true;
  startCapture();
});

// ***********************************************8
// ============clear-================
// ***********************************************8
clearBtn.on("click", function () {
  resetData();
  $(this).prop("disabled", true).hide();
});

function resetData() {
  enrollBtn.prop("disabled", true);
  verifyBtn.prop("disabled", true);
  captureBtn.prop("disabled", false);
  $(scannerImg).attr("src", "/Public/assets/img/finger.png");

  if (isVerification) {
    verificationPng = null;
  } else {
    isCaptured.left = [];
    isCaptured.right = [];
    $(handChildren).each((index) => {
      let finger = handChildren[index];
      $(finger).removeClass("green").removeClass("active");
    });
    bioHand.each((index) => {
      let hand = bioHand[index];
      $(hand).removeClass("green").removeClass("active");
    });
    currentHand = 0;
    currentFinger = 0;
  }
  printPreview.html("");
  stopCapture();
  setTimeout(() => {
    $(enrollBtn).prop("disabled", true);
  }, 1000);
}

// Is capturing fingerprint
function isCapturing() {
  if (test.acquisitionStarted) {
    captureBtn.prop("disabled", true);
    scannerImg.attr("src", "/Public/assets/img/scanning.gif");
    if (!isVerification) getHand();
    clearBtn.fadeIn().prop("disabled", false);
  }
}

function sampleAcquired(prop) {
  currentFinger++;
  let sample = JSON.parse(prop.samples);
  sample = Fingerprint.b64UrlTo64(sample[0]);

  let png = "data:image/png;base64,";

  if (isVerification) {
    scannerImg.attr("src", `${png}${sample}`);
    verificationPng = {
      finger: "Any",
      data: sample,
    };
    istoVerify = true;
    stopCapture();
    console.log(verificationPng);
    return;
  }
  printPreview.append(`<img src='${png}${sample}' />`);

  // let data = {data: sample, finger:currentFingerName}
  if (currentHand == 0) {
    let data = `"left_${currentFingerName}_finger_base64": "${sample}",`;
    isCaptured.left += data;
  } else if (currentHand == 1) {
    let data = `"right_${currentFingerName}_finger_base64": "${sample}",`;
    isCaptured.right += data;
  }

  if (currentFinger > 4 && currentFinger <= 5) {
    currentHand++;
  }

  getHand();
}
// getHand()
function getHand() {
  // let handChildren = $(selectedHand).find('.fingerprint_scanned').find('.print-container')
  let isFinger = handChildren[currentFinger];
  let fingerCaptured = handChildren[currentFinger - 1]; // Already captured Finger

  // Get finger name user is capturing
  currentFingerName = $(isFinger).data("finger");

  $(fingerCaptured).removeClass("active").addClass("green"); // Remove active class and set to captured
  $(isFinger).addClass("active"); // set current finger to capture to active

  let capturingHand = bioHand[currentHand]; // Get current hand user is capturing
  let capturedHand = bioHand[currentHand - 1]; // Get already captured hand
  if (currentFinger == handChildren.length) {
    $(capturingHand).removeClass("active").addClass("green");
    stopCapture();
  } else if (capturedHand) {
    $(capturedHand).removeClass("active").addClass("green");
  } else {
    $(capturingHand).addClass("active");
  }
  //   // console.log(selectedHand
  //   console.log(capturingHand);
}

// Enroll Fingerprint
enrollBtn.on("click", function () {
  let btn = $(this)
  enrollFingerprint(btn);
});

// Verify fingerprint
verifyBtn.on("click", function () {
  verifyFingerPrint();
});

// Enroll Fingers
function enrollFingerprint(btn) {
  const { left, right } = isCaptured;

  let leftHand = JSON.parse(`{${left.slice(0, -1)}}`);
  let rightHand = JSON.parse(`{${right.slice(0, -1)}}`);

  const item = {
    enrollment_id: bruiz.enrollmentId,
    ...leftHand,
    ...rightHand,
  };

  const formData = new FormData();
  for (const data in item) {
    if (data == "enrollment_id") {
      formData.append(data, item[data]);
    } else {
      formData.append("file", item[data]);
    }
  }

  // bruiz.requestEngine(bruiz.apiUri+"users/people", formData);
  bruiz.requestEngine(bruiz.apiUri, item, btn);
}

function verifyFingerPrint() {
  const formData = new FormData();
  formData.append("enrollment_id", bruiz.enrollmentId);
  formData.append("file", verificationPng.data);

  bruiz.requestEngine(
    bruiz.apiUri+"users/verify",
    formData,
    "Successfuly Verified",
    "Authorization failed"
  );
}


