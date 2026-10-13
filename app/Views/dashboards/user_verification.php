
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>OTP Validation</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    /* Global Styles */
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(135deg, #bd8e59ff 0%, #29bad0ff 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      color: #333;
    }

    .container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    h1 {
      font-size: 1.8rem;
      margin-bottom: 25px;
      color: #2575fc;
    }

    label {
      display: block;
      text-align: left;
      font-weight: 500;
      margin-bottom: 5px;
    }

    input {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    input:focus {
      border-color: #2575fc;
      box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #2575fc;
      border: none;
      border-radius: 8px;
      color: #fff;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }

    button:hover {
      background: #6a11cb;
      transform: translateY(-2px);
    }

    #otp-box {
      display: none;
      margin-top: 20px;
    }

    #otp-message {
      color: #28a745;
      font-weight: 500;
      display: none;
      margin-bottom: 15px;
      text-align: center;
    }

    .alert-error {
      color: #dc3545;
      font-weight: 500;
      margin-bottom: 15px;
      display: none;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>OTP Validation</h1>

  <!-- STEP 1: Email -->
  <div id="step-email-ip">
    <label>Email:</label>
    <input type="email" id="input_email" placeholder="Enter your email" required>
    <button type="button" onclick="sendOtp()">Send OTP</button>
  </div>

  <div id="otp-message">✅ OTP sent successfully! Please check your email.</div>
  <div class="alert-error" id="otp-error">❌ Something went wrong!</div>

  <!-- STEP 2: OTP -->
  <div id="otp-box">
    <label>Enter OTP:</label>
    <input type="text" id="input_otp" placeholder="Enter OTP">
    <button type="button" onclick="verifyOtp()">Verify OTP</button>
  </div>
</div>


<script>
function sendOtp() {
  const email = document.getElementById('input_email').value.trim();

  if (!email) {
    alert("Please enter email.");
    return;
  }

  fetch("http://localhost/call2.php?action=send_otp", {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: email })
  })
  .then(res => res.json())
  .then(data => {
    console.log("Send OTP Response:", data);
    if (data.status === 'success') {
      document.getElementById('step-email-ip').style.display = 'none';
      document.getElementById('otp-message').style.display = 'block';
      document.getElementById('otp-box').style.display = 'block';
    } else {
      alert(data.description || 'OTP sending failed!');
    }
  })
  .catch(err => {
    console.error("Send OTP Error:", err);
    alert('Something went wrong while sending OTP.');
  });
}

// function verifyOtp() {
//   const email = document.getElementById('input_email').value.trim();
//   const otp = document.getElementById('input_otp').value.trim();

//   if (!email || !otp) {
//     alert("Please enter both Email and OTP.");
//     return;
//   }

//   fetch("http://localhost/call2.php?action=verify_otp", {
//     method: 'POST',
//     headers: { 'Content-Type': 'application/json' },
//     body: JSON.stringify({ email: email, otp: otp })
//   })
//   .then(res => res.json())
//   .then(data => {
//     console.log("Verify OTP Response:", data);
//     if (data.status === 'success') {
//       alert("✅ OTP Verified!");
//       document.getElementById('otp-box').style.display = 'none';
//       document.getElementById('otp-message').style.display = 'none';
//       // Show next step if needed
//     } else {
//       alert(data.description || '❌ Invalid OTP!');
//     }
//   })
//   .catch(err => {
//     console.error("Verify OTP Error:", err);
//     alert('Verification failed!');
//   });
// }
function verifyOtp() {
  const email = document.getElementById('input_email').value.trim();
  const otp = document.getElementById('input_otp').value.trim();

  if (!email || !otp) {
    alert("Please enter both Email and OTP.");
    return;
  }

  fetch("http://localhost/call2.php?action=verify_otp", {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: email, otp: otp })
  })
  .then(res => res.json())
  .then(data => {
    console.log("Verify OTP Response:", data);
    if (data.status === 'success') {
      alert("✅ OTP Verified!");
      // Hide OTP box and message
      document.getElementById('otp-box').style.display = 'none';
      document.getElementById('otp-message').style.display = 'none';
      
      // ✅ Redirect to user_details page
      // window.location.href = "http://localhost/index.php/user_details";
      window.location.href = "<?= site_url('user_details/validation_index') ?>";

    } else {
      alert(data.description || '❌ Invalid OTP!');
    }
  })
  .catch(err => {
    console.error("Verify OTP Error:", err);
    alert('Verification failed!');
  });
}

</script>

</body>
</html>
