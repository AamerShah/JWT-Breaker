<?php
// Read the weak secrets from file and trim empty lines
$weak_secrets = file_get_contents('weak_secrets.txt');
$weak_secrets_array = array_filter(array_map('trim', explode("\n", $weak_secrets)));
$weak_secrets_json = json_encode(array_values($weak_secrets_array));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JWT-Breaker</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #3C2A21; /* Dark Brown */
      color: #E5E5CB; /* Light Beige */
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
      box-sizing: border-box;
      /* Background pattern from old index */
      background-image: 
          linear-gradient(30deg, #2C1F16 12%, transparent 12.5%, transparent 87%, #2C1F16 87.5%, #2C1F16),
          linear-gradient(150deg, #2C1F16 12%, transparent 12.5%, transparent 87%, #2C1F16 87.5%, #2C1F16),
          linear-gradient(30deg, #2C1F16 12%, transparent 12.5%, transparent 87%, #2C1F16 87.5%, #2C1F16),
          linear-gradient(150deg, #2C1F16 12%, transparent 12.5%, transparent 87%, #2C1F16 87.5%, #2C1F16),
          linear-gradient(90deg, #2C1F16 12%, transparent 12.5%, transparent 87%, #2C1F16 87.5%, #2C1F16),
          linear-gradient(#3C2A21 25%, #2C1F16 25%, #2C1F16 50%, #3C2A21 50%, #3C2A21 75%, #2C1F16 75%, #2C1F16);
      background-size: 80px 80px;
      background-position: 0 0, 0 0, 40px 40px, 40px 40px, 0 0, 0 0;
    }
    .container {
      background-color: rgba(60, 42, 33, 0.9); /* Semi-transparent dark brown */
      padding: 30px;
      border-radius: 12px;
      width: 100%;
      max-width: 1200px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.6);
      display: flex;
      flex-direction: column;
      gap: 20px;
      border: 1px solid #7F4F24; /* Accent border */
    }
    h3 {
      text-align: center;
      color: #FFC06C; /* Lighter Orange/Gold Accent */
      margin-bottom: 25px;
      font-size: 32px;
      text-transform: uppercase;
      letter-spacing: 3px;
      font-weight: 700;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    textarea, input[type="text"], select, input[type="number"] {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      background-color: #5B443B; /* Slightly lighter brown for inputs */
      color: #E5E5CB;
      border: 1px solid #7F4F24;
      border-radius: 8px;
      box-sizing: border-box;
      resize: vertical;
      transition: all 0.3s ease;
    }
    textarea::placeholder, input[type="text"]::placeholder, input[type="number"]::placeholder {
      color: #C0C0B2;
    }
    textarea:focus, input[type="text"]:focus, select:focus, input[type="number"]:focus {
      outline: none;
      border-color: #FFC06C;
      box-shadow: 0 0 10px rgba(255, 192, 108, 0.4);
    }
    .control-row {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: center;
    }
    .control-row > *, .input-group > * {
      flex: 1;
      min-width: 150px;
    }
    .input-group {
      display: flex;
      gap: 15px;
    }
    #secretControls {
      display: none;
      gap: 15px;
      align-items: stretch;
    }
    #secretControls > * {
      flex: 1;
      min-height: 50px;
      box-sizing: border-box;
    }
    .result-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }
    .section {
      background-color: #5B443B; /* Darker input background */
      padding: 20px;
      border-radius: 8px;
      box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.3);
      display: flex;
      flex-direction: column;
      min-height: 150px;
      border: 1px solid #7F4F24;
    }
    .section strong {
      color: #FFC06C;
      margin-bottom: 10px;
      font-size: 18px;
    }
    .section textarea {
      flex-grow: 1;
      min-height: 80px;
      margin-bottom: 0;
    }
    .section.success {
      background-color: #5cb85c; /* Green for success */
      box-shadow: 0 0 15px rgba(92, 184, 92, 0.5);
    }
    .section.running {
      background-color: #f0ad4e; /* Orange for running */
      box-shadow: 0 0 15px rgba(240, 173, 78, 0.5);
    }
    button {
      padding: 12px 20px;
      font-size: 18px;
      cursor: pointer;
      background-color: #FFC06C;
      color: #3C2A21;
      border: none;
      border-radius: 8px;
      transition: background-color 0.3s ease, transform 0.2s ease;
      font-weight: 700;
      min-width: 100px; /* Make button slimmer */
    }
    button:hover {
      background-color: #FFD49A;
      transform: translateY(-2px);
    }
    button:active {
      transform: translateY(0);
    }
    button:disabled {
        background-color: #7F4F24;
        color: #C0C0B2;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    #status {
      height: 2em;
      overflow-x: auto;
      white-space: nowrap;
      padding: 15px;
      background-color: #5B443B;
      color: #E5E5CB;
      font-size: 16px;
      border-radius: 8px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      border: 1px solid #7F4F24;
    }
    #status.info { background-color: #f0ad4e; color: #3C2A21; } /* Orange for info */
    #status.success { background-color: #5cb85c; color: #3C2A21; } /* Green for success */
    #status.error { background-color: #d9534f; color: #E5E5CB; } /* Red for error */

    #currentAttempt {
      margin-top: 10px;
      font-family: monospace;
      font-size: 16px;
      background-color: #2C1F16; /* Even darker for contrast */
      color: #F0F0E0;
      padding: 10px;
      border-radius: 8px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      border: 1px solid #4F382E;
    }
    input[type="file"] {
      background-color: #5B443B;
      color: #E5E5CB;
      border: 1px solid #7F4F24;
      padding: 11px;
      border-radius: 8px;
      cursor: pointer;
      line-height: 1.5;
    }
    input[type="file"]::file-selector-button {
      background-color: #FFC06C;
      color: #3C2A21;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
      margin-right: 10px;
      transition: background-color 0.3s ease;
    }
    input[type="file"]::file-selector-button:hover {
      background-color: #FFD49A;
    }

    /* Vulnerabilities Section */
    .vulnerabilities-section {
      background-color: rgba(60, 42, 33, 0.9);
      border-radius: 12px;
      overflow: hidden;
      margin-top: 20px;
      border: 1px solid #7F4F24;
    }
    .vulnerabilities-header {
      background-color: #5B443B;
      color: #FFC06C;
      padding: 15px 20px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 20px;
      font-weight: 700;
      border-bottom: 1px solid #7F4F24;
    }
    .vulnerabilities-header:hover {
        background-color: #7F4F24;
    }
    .vulnerabilities-header .arrow {
        transition: transform 0.3s ease;
    }
    .vulnerabilities-header.expanded .arrow {
        transform: rotate(90deg);
    }
    .vulnerabilities-content {
      padding: 0 20px;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out, padding 0.3s ease-out;
    }
    .vulnerabilities-content.expanded {
      max-height: 500px; /* Arbitrary large value, adjust if needed */
      padding: 20px;
    }
    .vulnerabilities-content ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .vulnerabilities-content li {
      margin-bottom: 10px;
      padding-left: 25px;
      position: relative;
      color: #E5E5CB;
    }
    .vulnerabilities-content li:last-child {
      margin-bottom: 0;
    }
    .vulnerabilities-content li::before {
      content: '•';
      color: #FFD49A; /* Accent color for bullet points */
      font-size: 1.2em;
      position: absolute;
      left: 0;
      top: 0;
    }

    /* Global Tooltip styles */
    #globalTooltip {
        position: fixed;
        top: 20px; /* Distance from top */
        left: 50%;
        transform: translateX(-50%);
        background-color: #2C1F16; /* Dark background for tooltip */
        color: #FFD49A; /* Light accent color for text */
        text-align: center;
        border-radius: 8px;
        padding: 15px 25px; /* Bigger padding */
        z-index: 1000; /* Ensure it's on top of everything */
        font-size: 18px; /* Bigger font size */
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        max-width: 500px; /* Max width for readability */
        white-space: normal;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        border: 1px solid #7F4F24;
    }
    
    /* Labels for brute force inputs */
    .brute-force-field {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%; /* Ensure it takes full width in its container */
    }
    .brute-force-field label {
        flex-shrink: 0;
        color: #FFC06C;
        font-size: 16px;
        font-weight: bold;
        min-width: 90px; /* Adjust as needed for alignment */
    }
    .brute-force-field input {
        flex-grow: 1;
        min-width: unset; /* Override default flex 1 min-width */
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
      .container {
        padding: 20px;
      }
      .control-row {
        flex-direction: column;
        gap: 15px;
      }
      .control-row > *, .input-group > * {
        min-width: unset;
        width: 100%;
      }
      .input-group {
        flex-direction: column;
        gap: 15px;
      }
      #secretControls {
        flex-direction: column;
        gap: 15px;
      }
      .result-container {
        grid-template-columns: 1fr;
      }
      .section {
        min-height: unset;
      }
      #globalTooltip {
        width: 90%;
        left: 5%;
        transform: translateX(0);
        top: 10px; /* Adjust top for smaller screens */
        padding: 10px 15px;
        font-size: 16px;
      }
      .brute-force-field {
        flex-direction: column;
        align-items: flex-start;
      }
      .brute-force-field label {
        min-width: unset;
      }
    }
  </style>
  <!-- Main thread libraries using the specified jsrsasign URL -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
  <script src="jsrsasign-all-min.js"></script>
</head>
<body>
  <div id="globalTooltip"></div> <!-- Global tooltip container -->

  <div class="container">
    <h3>JWT-Breaker</h3>
    <textarea id="jwtToken" placeholder="Paste JWT here" oninput="decodeJWT()" data-tooltip="Paste your JSON Web Token (JWT) string here. The tool will automatically parse its header, payload, and signature for analysis."></textarea>
    
    <div class="control-row">
      <select id="attackType" onchange="toggleAttackType()" data-tooltip="Select the type of attack: 'Default Dictionary' uses a pre-loaded list of common weak secrets; 'Custom Dictionary' allows you to provide your own list; 'Brute Force' generates secrets based on character sets and lengths.">
        <option value="dict">Default Dictionary</option>
        <option value="custom">Custom Dictionary</option>
        <option value="brute">Brute Force</option>
      </select>
      <select id="algorithmSelect" data-tooltip="Choose the hashing algorithm used to sign the JWT. This tool supports HS256, HS384, and HS512.">
        <!-- Only supported algorithms -->
        <option value="HS256">HS256</option>
        <option value="HS384">HS384</option>
        <option value="HS512">HS512</option>
      </select>
      <input type="number" id="threadsInput" min="1" value="30" placeholder="Threads" data-tooltip="Set the number of worker threads to use for cracking. More threads can utilize more CPU cores, potentially speeding up the attack process.">
      <input type="number" id="delayInput" min="0" value="0" placeholder="Delay (ms)" data-tooltip="Define a delay in milliseconds between attempts for each worker thread. Set to 0 for the fastest execution with no delay. Increasing this value can reduce CPU usage.">
      <button id="executeBtn" onclick="toggleAttack()" data-tooltip="Click to start the attack. If an attack is running, this button will pause it. If paused, click to resume."></button>
    </div>
    
    <div id="secretControls">
      <textarea id="secretList" placeholder="Enter keys separated by ',' or new lines." data-tooltip="Enter a custom list of potential secrets (passwords or keys) for dictionary attacks. Separate each secret with a comma or a new line."></textarea>
      <input type="file" id="dictFile" accept=".txt" onchange="loadDictFile()" data-tooltip="Upload a plain text file (.txt) containing a list of secrets, with one secret per line. This file will be used for custom dictionary attacks.">
    </div>
    
    <div id="bruteControls" style="display:none">
      <div class="control-row">
        <div class="input-group">
          <div class="brute-force-field">
            <label for="minLength">Min Length:</label>
            <input type="number" id="minLength" min="1" max="10" value="1">
          </div>
          <div class="brute-force-field">
            <label for="maxLength">Max Length:</label>
            <input type="number" id="maxLength" min="1" max="10" value="7">
          </div>
        </div>
        <div class="brute-force-field">
          <label for="charSet">Character Set:</label>
          <input type="text" id="charSet" value="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789">
        </div>
      </div>
    </div>
    
    <div id="status" class="status" data-tooltip="Displays the current status of the JWT analysis or cracking process.">Status: Waiting...</div>
    <div id="currentAttempt" data-tooltip="Shows the current secret being attempted during dictionary or brute-force attacks.">Current Attempt: </div>
    
    <div class="result-container">
      <div class="section">
        <strong>Header</strong>
        <textarea id="headerText" oninput="updateJWT()" data-tooltip="Displays the decoded header of the JWT. You can edit this to modify the JWT properties."></textarea>
      </div>
      <div class="section">
        <strong>Payload</strong>
        <textarea id="payloadText" oninput="updateJWT()" data-tooltip="Displays the decoded payload (claims) of the JWT. You can edit this to modify the token's data."></textarea>
      </div>
      <div class="section" id="signatureSection">
        <strong>Signature</strong>
        <textarea id="signatureText" readonly data-tooltip="Displays the signature part of the JWT. This field is read-only as it is the target for cracking."></textarea>
      </div>
    </div>

    <div class="vulnerabilities-section">
        <div class="vulnerabilities-header" onclick="toggleVulnerabilities()" data-tooltip="Click to expand or collapse a list of potential security vulnerabilities detected in the current JWT.">
            <span>Potential Vulnerabilities</span>
            <span class="arrow">&gt;</span>
        </div>
        <div class="vulnerabilities-content">
            <ul id="vulnerabilityList">
                <!-- Vulnerabilities will be populated here by JavaScript -->
            </ul>
        </div>
    </div>
  </div>
  
  <script>
    // Global variables
    const weakSecrets = <?php echo $weak_secrets_json; ?>;
    let workers = [];
    let found = false;
    let attackState = 'idle'; // 'idle', 'running', 'paused'
    let isJwtValid = false; // Flag to track if JWT is properly decoded
    let workerProgress = {}; // Store last known progress for each worker (index in list or brute force counter)
    let workerConfig = null; // Store the last configuration used for workers

    // Base64url helper functions
    function base64UrlEncode(str) {
      return btoa(unescape(encodeURIComponent(str)))
        .replace(/\+/g, '-')
        .replace(/\//g, '_')
        .replace(/=+$/, '');
    }
    function base64UrlDecode(str) {
      str = str.replace(/-/g, '+').replace(/_/g, '/');
      while (str.length % 4) { str += '='; }
      try {
        return decodeURIComponent(escape(atob(str)));
      } catch (e) {
        console.error("Error decoding base64Url:", e, str);
        // Fallback for cases where decodeURIComponent/escape fails, might return garbled but won't crash
        return atob(str);
      }
    }

    function playSuccessSound() {
      // You would need to provide a 'success.mp3' file or use another audio source
      const audio = new Audio('success.mp3'); 
      audio.play().catch(e => console.error("Error playing sound:", e));
    }

    // Decode JWT and update the UI
    function decodeJWT() {
      const token = document.getElementById('jwtToken').value.trim();
      const parts = token.split('.');
      clearJWTDisplay(); // Clear display before attempting to decode

      if (parts.length !== 3) {
        updateStatus("Invalid JWT format. Please paste a valid JWT.", "error");
        isJwtValid = false;
        updateVulnerabilities([]); // Clear vulnerabilities on invalid token
        return;
      }
      try {
        const header = JSON.parse(base64UrlDecode(parts[0]));
        const payload = JSON.parse(base64UrlDecode(parts[1]));
        document.getElementById('headerText').value = JSON.stringify(header, null, 2);
        document.getElementById('payloadText').value = JSON.stringify(payload, null, 2);
        document.getElementById('signatureText').value = parts[2];
        document.getElementById('algorithmSelect').value = header.alg || 'HS256';
        updateStatus("JWT decoded successfully.", "info");
        isJwtValid = true;
        checkVulnerabilities(header, payload, parts[2]); // Check for vulnerabilities after decoding
      } catch (error) {
        updateStatus("Error decoding JWT: " + error.message, "error");
        isJwtValid = false;
        updateVulnerabilities([]);
      }
    }

    // Update JWT when header or payload changes using proper base64url encoding
    function updateJWT() {
      try {
        const header = JSON.parse(document.getElementById('headerText').value);
        const payload = JSON.parse(document.getElementById('payloadText').value);
        const signature = document.getElementById('signatureText').value;
        const encodedHeader = base64UrlEncode(JSON.stringify(header));
        const encodedPayload = base64UrlEncode(JSON.stringify(payload));
        document.getElementById('jwtToken').value = `${encodedHeader}.${encodedPayload}.${signature}`;
        updateStatus("JWT updated. Remember to re-sign if header/payload changed.", "info");
        isJwtValid = true; // Assume valid after manual update, unless parsing fails
        checkVulnerabilities(header, payload, signature);
      } catch (error) {
        updateStatus("Error updating JWT: " + error.message, "error");
        isJwtValid = false;
      }
    }

    function clearJWTDisplay() {
      document.getElementById('headerText').value = '';
      document.getElementById('payloadText').value = '';
      document.getElementById('signatureText').value = '';
    }

    function toggleAttackType() {
      const attackType = document.getElementById('attackType').value;
      document.getElementById('bruteControls').style.display = attackType === 'brute' ? 'block' : 'none';
      document.getElementById('secretControls').style.display = attackType === 'custom' ? 'flex' : 'none';
    }

    function loadDictFile() {
      const file = document.getElementById('dictFile').files[0];
      if (!file) {
        updateStatus("No file selected.", "error");
        return;
      }
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('secretList').value = e.target.result;
        updateStatus("Dictionary file loaded.", "info");
      };
      reader.onerror = e => {
        updateStatus("Error reading file: " + e.message, "error");
      };
      reader.readAsText(file);
    }

    // Update status message for key found or errors only
    function updateStatus(message, type) {
      const statusDiv = document.getElementById('status');
      statusDiv.textContent = message;
      statusDiv.className = `status ${type}`; // Apply classes based on type
      // Remove running/success classes from sections unless it's a success or info state
      document.querySelectorAll('.section').forEach(s => {
        s.classList.remove('success', 'running');
        if (type === 'success') {
            s.classList.add('success');
        } else if (type === 'info' && message.includes("Starting")) { // Only add running class at the start of an attack
            s.classList.add('running');
        }
      });
    }

    // Update the current attempt display (overwriting previous value)
    function updateCurrentAttempt(candidate) {
      const displayDiv = document.getElementById('currentAttempt');
      // Truncate long candidates for display without affecting layout
      const maxLength = 50; 
      const displayText = candidate.length > maxLength ? candidate.substring(0, maxLength - 3) + '...' : candidate;
      displayDiv.textContent = "Current Attempt: " + displayText;
    }

    // Function to enable/disable specific UI elements
    function toggleInputStates(disabled) {
        document.getElementById('attackType').disabled = disabled;
        document.getElementById('algorithmSelect').disabled = disabled;
        document.getElementById('threadsInput').disabled = disabled;
        document.getElementById('delayInput').disabled = disabled;

        const attackType = document.getElementById('attackType').value;
        if (attackType === 'custom') {
            document.getElementById('secretList').disabled = disabled;
            document.getElementById('dictFile').disabled = disabled;
        } else if (attackType === 'brute') {
            document.getElementById('minLength').disabled = disabled;
            document.getElementById('maxLength').disabled = disabled;
            document.getElementById('charSet').disabled = disabled;
        }
        // JWT Token input, header, payload, signature remain enabled for inspection
    }


    // Main function to start, pause, or resume the attack
    function toggleAttack() {
        const executeBtn = document.getElementById('executeBtn');

        if (!isJwtValid && attackState === 'idle') {
            updateStatus("Cannot start attack: Please paste a valid JWT token first.", "error");
            return;
        }

        if (attackState === 'idle') {
            // Start a new attack
            found = false;
            stopWorkers(); // Ensure all previous workers are terminated
            workerProgress = {}; // Reset progress for a new attack
            executeBtn.textContent = 'Pause';
            attackState = 'running';
            document.getElementById('currentAttempt').textContent = "Current Attempt: ";
            toggleInputStates(true); // Disable other inputs

            const token = document.getElementById('jwtToken').value.trim();
            const parts = token.split('.');
            const algorithm = document.getElementById('algorithmSelect').value;
            const attackType = document.getElementById('attackType').value;
            const delay = Math.max(0, parseInt(document.getElementById('delayInput').value, 10));
            const totalWorkers = Math.max(1, parseInt(document.getElementById('threadsInput').value, 10));

            workerConfig = {
                header: parts[0],
                payload: parts[1],
                signature: parts[2],
                algorithm: algorithm,
                delay: delay,
                totalWorkers: totalWorkers,
                attackType: attackType
            };

            if (attackType === 'dict' || attackType === 'custom') {
                let candidateList = (attackType === 'custom') ? 
                    document.getElementById('secretList').value.split(/[\n,]+/).map(s => s.trim()).filter(s => s) : weakSecrets;
                if (candidateList.length === 0) {
                    updateStatus("No keys provided for dictionary attack.", "error");
                    resetAttackState();
                    return;
                }
                workerConfig.list = candidateList;
                updateStatus("Starting dictionary attack with " + candidateList.length + " keys...", "info");
                startWorkers(true); // true for new attack
            } else if (attackType === 'brute') {
                const minLen = parseInt(document.getElementById('minLength').value, 10);
                const maxLen = parseInt(document.getElementById('maxLength').value, 10);
                if (isNaN(minLen) || minLen < 1 || minLen > 10 ||
                    isNaN(maxLen) || maxLen < 1 || maxLen > 10 ||
                    minLen > maxLen) {
                    updateStatus("Invalid length parameters for brute force (1-10 characters allowed).", "error");
                    resetAttackState();
                    return;
                }
                workerConfig.minLen = minLen;
                workerConfig.maxLen = maxLen;
                workerConfig.charset = document.getElementById('charSet').value;
                updateStatus("Starting brute force attack...", "info");
                startWorkers(true); // true for new attack
            } else {
                updateStatus("Invalid attack type selected.", "error");
                resetAttackState();
            }

        } else if (attackState === 'running') {
            // Pause the attack
            attackState = 'paused';
            executeBtn.textContent = 'Resume';
            updateStatus("Attack paused. Click Resume to continue.", "info");
            workers.forEach(w => w.postMessage({ command: 'pause' }));

        } else if (attackState === 'paused') {
            // Resume the attack
            attackState = 'running';
            executeBtn.textContent = 'Pause';
            updateStatus("Attack resumed...", "info");
            startWorkers(false); // false for resuming existing attack
        }
    }

    function resetAttackState() {
        attackState = 'idle';
        document.getElementById('executeBtn').textContent = 'Execute';
        stopWorkers();
        workerProgress = {};
        workerConfig = null;
        toggleInputStates(false); // Enable all inputs
    }

    function stopWorkers() {
        workers.forEach(w => {
            try { w.terminate(); } catch (e) { console.error("Error terminating worker:", e); }
        });
        workers = [];
    }

    // Spawns/resumes workers based on the stored workerConfig
    function startWorkers(isNewAttack) {
      if (!workerConfig) return; // Should not happen if called correctly

      const { header, payload, signature, algorithm, delay, totalWorkers, attackType } = workerConfig;
      const data = header + "." + payload;

      // Define worker code as a string
      const workerCode = `
        self.window = self; // Mock window for jsrsasign
        importScripts(
          'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js',
          'jsrsasign-all-min.js'
        );
        
        // Correct base64url encoding for CryptoJS WordArrays
        function base64url(wordArray) {
          return CryptoJS.enc.Base64.stringify(wordArray)
            .replace(/\\+/g, '-')
            .replace(/\\//g, '_')
            .replace(/=+$/, '');
        }

        // Sign function for worker
        function sign(algorithm, secret, data) {
          if (algorithm.startsWith("HS")) {
            const hash = CryptoJS['HmacSHA' + algorithm.slice(2)](data, secret);
            return base64url(hash);
          }
          try {
            const sig = new KJUR.crypto.Signature({ alg: algorithm });
            sig.init({ alg: algorithm, prvkey: secret });
            sig.updateString(data);
            return sig.sign();
          } catch (e) {
            return null; 
          }
        }
        
        // Brute force candidate generator
        function generateSecret(num, charset, minLen, maxLen) {
          let currentTotalCombinations = 0;
          for (let len = minLen; len <= maxLen; len++) {
            const combinationsForThisLength = Math.pow(charset.length, len);
            
            if (num < currentTotalCombinations + combinationsForThisLength) {
              let pos = num - currentTotalCombinations;
              let candidate = "";
              for (let i = 0; i < len; i++) {
                candidate = charset[pos % charset.length] + candidate;
                pos = Math.floor(pos / charset.length);
              }
              return candidate;
            }
            currentTotalCombinations += combinationsForThisLength;
            
            if (currentTotalCombinations > Number.MAX_SAFE_INTEGER / charset.length && len < maxLen) {
                return null; 
            }
          }
          return null;
        }

        // Worker's internal state variables, declared in the global scope of the worker
        self.isPaused = false; 
        self.found = false;
        self.workerConfig = null; 
        self.currentData = null; 
        self.index = 0; // Current index for this worker
        self.iterations = 0; // Iterations count for progress reporting

        // The core processing loop for the worker, now a global function
        function processCandidate() {
            if (self.found || self.isPaused) {
                return; // Stop if found or paused
            }
            
            self.iterations++;

            let candidate;
            if (self.workerConfig.attackType === 'dict' || self.workerConfig.attackType === 'custom') {
                if (self.index >= self.workerConfig.list.length) {
                    postMessage({ done: true, workerId: self.workerConfig.workerId }); // Send workerId with done
                    return;
                }
                candidate = self.workerConfig.list[self.index];
            } else if (self.workerConfig.attackType === 'brute') {
                candidate = generateSecret(self.index, self.workerConfig.charset, self.workerConfig.minLen, self.workerConfig.maxLen);
                if (!candidate) {
                    postMessage({ done: true, workerId: self.workerConfig.workerId }); // Send workerId with done
                    return;
                }
            }

            // Report progress more frequently (every 10 iterations)
            if (self.iterations % 10 === 0) { 
                postMessage({ progress: candidate, workerId: self.workerConfig.workerId, currentIndex: self.index });
            }
            
            try {
                const calculatedSignature = sign(self.workerConfig.algorithm, candidate, self.currentData);
                if (calculatedSignature && calculatedSignature === self.workerConfig.signature) {
                    postMessage({ found: candidate });
                    self.found = true;
                    return;
                }
            } catch (err) { /* console.warn("Worker candidate error:", err); */ }

            self.index += self.workerConfig.totalWorkers; // Move to the next candidate for this worker
            
            if (!self.found && !self.isPaused) {
                setTimeout(processCandidate, self.workerConfig.delay);
            }
        }

        // Message handler for the worker
        onmessage = function(e) {
            if (e.data.command === 'pause') {
                self.isPaused = true;
                return;
            }
            if (e.data.command === 'resume') {
                self.isPaused = false;
                if (!self.found) { // Only resume if not already found
                    processCandidate(); // Kick off the loop again
                }
                return;
            }

            // Initial configuration for the worker (only sent once per new attack)
            self.workerConfig = e.data;
            self.currentData = self.workerConfig.header + "." + self.workerConfig.payload;
            self.index = self.workerConfig.startIndex; // Initialize index from startIndex
            self.iterations = 0; // Reset iterations for new attack
            self.found = false; // Reset found status for new attack
            self.isPaused = false; // Ensure not paused initially

            processCandidate(); // Start the processing loop
        };
      `;

      const blob = new Blob([workerCode], { type: 'application/javascript' });
      const workerURL = URL.createObjectURL(blob);
      let workersDoneCount = 0;

      // If it's a new attack, terminate existing workers and reset state
      if (isNewAttack) {
          stopWorkers(); 
          workersDoneCount = 0;
          workers = []; // Ensure workers array is truly empty for new attack
          workerProgress = {}; // Ensure workerProgress is reset for a new attack
      }

      // Create or re-initialize workers
      for (let i = 0; i < totalWorkers; i++) {
        // If resuming, and worker already exists, don't create new one
        if (!isNewAttack && workers[i]) {
            workers[i].postMessage({ command: 'resume' });
            continue;
        }

        const worker = new Worker(workerURL);
        worker._workerId = i;
        
        worker.onerror = (e) => {
          console.error(`Worker ${worker._workerId} error:`, e);
          updateStatus(`Worker ${worker._workerId} error: ${e.message || "Unknown error"}. Stopping attack.`, "error");
          resetAttackState(); // Stop all and reset
        };

        worker.onmessage = (e) => {
          if (e.data.found && !found) {
            found = true;
            updateStatus("Key found: " + e.data.found, "success");
            playSuccessSound();
            resetAttackState(); // Found, so stop all and reset button
          } else if (e.data.progress) {
            updateCurrentAttempt(e.data.progress);
            // Store current progress for potential resume
            if (e.data.workerId !== undefined && e.data.currentIndex !== undefined) {
                workerProgress[e.data.workerId] = e.data.currentIndex;
            }
          } else if (e.data.done) {
            workersDoneCount++;
            if (!found && workersDoneCount === totalWorkers) {
              updateStatus("Attack completed. Key not found.", "error");
              resetAttackState(); // All workers done, no key found
            }
          }
        };
        
        // Pass the initial configuration (startIndex) to the worker
        const startIdx = isNewAttack ? i : (workerProgress[i] !== undefined ? workerProgress[i] : i);
        worker.postMessage({ ...workerConfig, workerId: i, startIndex: startIdx, isNewAttack: isNewAttack });
        workers.push(worker);
      }
    }

    // Global Tooltip Management
    const globalTooltip = document.getElementById('globalTooltip');
    let tooltipTimeout;

    function showGlobalTooltip(text) {
        clearTimeout(tooltipTimeout);
        globalTooltip.textContent = text;
        globalTooltip.style.opacity = '1';
        globalTooltip.style.visibility = 'visible';
    }

    function hideGlobalTooltip() {
        tooltipTimeout = setTimeout(() => {
            globalTooltip.style.opacity = '0';
            globalTooltip.style.visibility = 'hidden';
        }, 100); // Small delay before hiding
    }

    // Attach tooltip events
    document.addEventListener('DOMContentLoaded', () => {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(el => {
            el.addEventListener('mouseenter', () => {
                showGlobalTooltip(el.dataset.tooltip);
            });
            el.addEventListener('mouseleave', hideGlobalTooltip);
            // Also handle focus for accessibility
            el.addEventListener('focus', () => {
                showGlobalTooltip(el.dataset.tooltip);
            });
            el.addEventListener('blur', hideGlobalTooltip);
        });
        document.getElementById('executeBtn').textContent = 'Execute'; // Set initial button text
    });


    // Vulnerability Checker
    function checkVulnerabilities(header, payload, signature) {
        const vulnerabilities = [];

        // 1. Weak Secret Detection (implies if a dictionary attack might succeed)
        // This is primarily for informational purposes, the tool itself finds it.
        // We can't know *if* it's weak until we find it, but we can suggest the possibility.
        if (weakSecrets.length > 0) { // Check if the weak_secrets.txt had content
            vulnerabilities.push("Default dictionary of weak secrets is available. If the key is common, it might be easily guessable.");
        }

        // 2. Algorithm Confusion (None algorithm)
        if (header.alg && header.alg.toLowerCase() === 'none') {
            vulnerabilities.push("Algorithm set to 'None': This can allow an attacker to bypass signature verification entirely. Consider if this JWT is intended to be unsigned.");
        }

        // 3. Algorithm Confusion (HS256 with Public Key) - theoretical, needs backend check
        // This is hard to detect purely client-side without knowing the backend's public key.
        // We can only warn about the potential.
        if (header.alg && header.alg.startsWith('RS')) { // If it's an RSA token, might be vulnerable to alg confusion if backend uses HS for it.
             // This is a heuristic. A more robust check would involve trying to sign with a public key and HS256.
             vulnerabilities.push("Algorithm is " + header.alg + ". If the server verifies this with an RSA public key but allows HS256, it might be vulnerable to algorithm confusion attacks (using the public key as HMAC secret).");
        }
        
        // 4. Missing kid (Key ID) header - might indicate less robust key management
        if (!header.kid) {
            vulnerabilities.push("Missing 'kid' (Key ID) header: Can make key rotation and management harder, potentially leading to reuse of compromised keys.");
        }

        // 5. Expired Token (check 'exp' claim)
        if (payload.exp && typeof payload.exp === 'number') {
            const expirationTime = payload.exp * 1000; // Convert to milliseconds
            const currentTime = Date.now();
            if (expirationTime < currentTime) {
                vulnerabilities.push("Expired Token: The 'exp' (expiration time) claim indicates this token has already expired.");
            } else {
                const timeLeft = (expirationTime - currentTime) / (1000 * 60); // minutes
                if (timeLeft < 60 && timeLeft > 0) {
                    vulnerabilities.push(`Token expiring soon: The 'exp' claim indicates this token will expire in approximately ${Math.round(timeLeft)} minutes.`);
                }
            }
        }

        // 6. Not Before (nbf) claim in the future
        if (payload.nbf && typeof payload.nbf === 'number') {
            const notBeforeTime = payload.nbf * 1000;
            const currentTime = Date.now();
            if (notBeforeTime > currentTime) {
                vulnerabilities.push("Token not yet valid: The 'nbf' (not before) claim indicates this token is not valid until a future time.");
            }
        }

        // 7. Replay Attack Potential (lack of 'jti' - JWT ID)
        if (!payload.jti) {
            vulnerabilities.push("Missing 'jti' (JWT ID) claim: Without a unique ID, tokens might be susceptible to replay attacks if not otherwise handled (e.g., blacklist/whitelist).");
        }

        // 8. Insecure Header Parameters (e.g., jku, x5u, jwk, x5c pointing to untrusted sources)
        // This is a heuristic, as "insecure" depends on context.
        if (header.jku && !header.jku.startsWith('https://')) {
            vulnerabilities.push("Insecure 'jku' (JWK Set URL) header: Uses non-HTTPS URL. This can expose to MITM attacks for key retrieval.");
        }
        if (header.x5u && !header.x5u.startsWith('https://')) {
            vulnerabilities.push("Insecure 'x5u' (X.509 URL) header: Uses non-HTTPS URL. This can expose to MITM attacks for certificate retrieval.");
        }
        // jwk and x5c are harder to judge without deeper analysis.
        // For 'jwk', if it directly contains a weak key, that's an issue.
        // For 'x5c', if the certificate chain is untrusted.

        updateVulnerabilities(vulnerabilities);
    }

    function updateVulnerabilities(vulnerabilityList) {
        const listElement = document.getElementById('vulnerabilityList');
        listElement.innerHTML = ''; // Clear previous list

        if (vulnerabilityList.length === 0) {
            listElement.innerHTML = '<li>No common vulnerabilities detected based on static analysis.</li>';
        } else {
            vulnerabilityList.forEach(vuln => {
                const li = document.createElement('li');
                li.textContent = vuln;
                listElement.appendChild(li);
            });
        }
    }

    function toggleVulnerabilities() {
        const header = document.querySelector('.vulnerabilities-header');
        const content = document.querySelector('.vulnerabilities-content');
        header.classList.toggle('expanded');
        content.classList.toggle('expanded');
    }

    // Initialize UI on load
    document.addEventListener('DOMContentLoaded', () => {
        toggleAttackType(); // Set initial display for attack controls
        updateVulnerabilities([]); 
        decodeJWT(); // Attempt to decode any pre-filled JWT on load
    });

  </script>
</body>
</html>
