# JWT-Breaker 🔑

![Repo Size](https://img.shields.io/badge/Repo%20Size-1.5%20MB-blue.svg?style=for-the-badge)
![GitHub last commit](https://img.shields.io/github/last-commit/AamerShah/JWT-Breaker?style=for-the-badge)
![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)
![Built With: HTML, CSS, JS, PHP](https://img.shields.io/badge/Built%20With-HTML%2C%20CSS%2C%20JS%2C%20PHP-blue.svg?style=for-the-badge)

## Table of Contents

- [About JWT-Breaker](#about-jwt-breaker)
- [Features](#features)
- [Installation](#installation)
- [How to Use](#how-to-use)
- [Attack Modes](#attack-modes)
  - [1. Default Dictionary Attack](#1-default-dictionary-attack)
  - [2. Custom Dictionary Attack](#2-custom-dictionary-attack)
  - [3. Brute-Force Attack](#3-brute-force-attack)
- [Vulnerability Analysis](#vulnerability-analysis)
- [Technology Stack](#technology-stack)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

---

## About JWT-Breaker

JWT-Breaker is a powerful, client-side web application designed to aid in the security assessment of JSON Web Tokens (JWTs). It provides robust capabilities for decoding JWTs, performing various dictionary and brute-force attacks on HMAC-signed tokens, and identifying common JWT-related vulnerabilities.

Built with HTML, CSS, JavaScript (leveraging Web Workers for multi-threading), and a minimal PHP backend for dictionary management, JWT-Breaker runs directly in your browser, offering a convenient and accessible tool for penetration testers, security researchers, and developers looking to understand JWT security.

---

## Features

JWT-Breaker is packed with features to streamline your JWT analysis workflow:

* **⚡ Real-time JWT Decoding:** Instantly parse and display the Header, Payload, and Signature components of any JWT.
* **🔗 Interactive Token Manipulation:** Edit Header and Payload sections directly in the UI, and the token string updates automatically.
* **⚔️ Multiple Attack Modes:**
    * **Default Dictionary:** Utilize a pre-loaded list of common weak secrets.
    * **Custom Dictionary:** Upload your own wordlist via text input or file upload.
    * **Brute-Force:** Generate secrets based on configurable character sets, minimum, and maximum lengths.
* **🚀 Multi-threading with Web Workers:** Leverage your CPU's cores for significantly faster cracking attempts, configurable via a "Threads" input.
* **⏸️ Attack Control:** Pause and resume ongoing cracking operations.
* **⏱️ Configurable Delay:** Introduce a delay in milliseconds between attempts per worker to manage CPU usage.
* **✅ Supported Algorithms:** Specifically designed to crack HMAC-based signatures (HS256, HS384, HS512).
* **✨ Intuitive UI/UX:**
    * **Tooltips:** Detailed tooltips for every UI element provide guidance and clarity.
    * **Dynamic Status Updates:** Real-time feedback on the attack's progress, including current attempt and status messages.
    * **Input Disabling:** Relevant input fields are automatically disabled during an active attack to prevent misconfiguration.
* **🛡️ Basic Vulnerability Analysis:** Automatically flags common JWT misconfigurations and potential weaknesses (e.g., "alg: none" vulnerability, missing `kid`, token expiry, replay potential).
* **🔊 Success Notification:** An audible alert when a key is successfully found.

---

## Installation

JWT-Breaker is a web-based tool. To run it, you need a web server with PHP support (e.g., Apache, Nginx with PHP-FPM, or even PHP's built-in server).

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/AamerShah/JWT-Breaker.git](https://github.com/AamerShah/JWT-Breaker.git)
    ```
2.  **Navigate to the project directory:**
    ```bash
    cd JWT-Breaker
    ```
3.  **Place it on your web server:**
    Copy the `JWT-Breaker` folder to your web server's document root (e.g., `htdocs` for Apache, `www` for Nginx).

4.  **Access in browser:**
    Open your web browser and navigate to the URL where you placed the project (e.g., `http://localhost/JWT-Breaker/` or `http://your-server-ip/JWT-Breaker/`).

    Alternatively, for quick local testing (requires PHP installed):
    ```bash
    php -S localhost:8000
    ```
    Then, open `http://localhost:8000` in your browser.

---

## How to Use

1.  **Paste JWT:** In the large text area at the top, paste the JWT you wish to analyze or crack. The Header, Payload, and Signature sections will automatically populate.
2.  **Inspect & Modify (Optional):** Review the decoded Header and Payload. You can modify these fields, and the main JWT string will update accordingly (note: this does not change the signature's validity; you'll need to re-sign or crack the new token).
3.  **Select Attack Type:** Choose your preferred attack method from the dropdown:
    * `Default Dictionary`
    * `Custom Dictionary`
    * `Brute Force`
    (Refer to [Attack Modes](#attack-modes) for details on configuring each type).
4.  **Configure Attack Parameters:**
    * **Algorithm:** Select the signing algorithm (HS256, HS384, HS512). This should match the `alg` field in the JWT header for successful cracking.
    * **Threads:** Adjust the number of Web Workers to utilize for parallel processing. More threads can speed up the process but will consume more CPU.
    * **Delay (ms):** Set a delay in milliseconds between attempts for each worker. A higher value reduces CPU load but slows down the attack.
5.  **Execute/Pause/Resume:**
    * Click the **Execute** button to start the attack.
    * Once running, the button changes to **Pause**. Click to temporarily halt the attack.
    * If paused, the button changes to **Resume**. Click to continue the attack from where it left off.
    * All other configuration fields are disabled while an attack is active.
6.  **Monitor Status:**
    * The **Status** bar provides real-time updates on the cracking process.
    * The **Current Attempt** display shows the secret being tested by a worker.
7.  **Check Vulnerabilities:** Expand the "Potential Vulnerabilities" section to see a list of identified JWT weaknesses.

---

## Attack Modes

### 1. Default Dictionary Attack

This mode uses a pre-defined list of common weak passwords/secrets (`weak_secrets.txt` in the project root) to attempt to crack the JWT.

* **Configuration:** Simply select `Default Dictionary` from the "Attack Type" dropdown. No further input is required for the dictionary itself.

### 2. Custom Dictionary Attack

Allows you to provide your own list of potential secrets.

* **Configuration:**
    1.  Select `Custom Dictionary` from the "Attack Type" dropdown.
    2.  A `Secret List` textarea and a `dictFile` upload button will appear.
    3.  You can either:
        * Type or paste secrets directly into the `Secret List` textarea, separated by commas or new lines.
        * Click `Choose File` and upload a plain text file (`.txt`) where each secret is on a new line.

### 3. Brute-Force Attack

This mode generates secret keys character by character based on a specified charset and length range.

* **Configuration:**
    1.  Select `Brute Force` from the "Attack Type" dropdown.
    2.  `Min Length`, `Max Length`, and `Character Set` fields will appear.
    3.  **Min Length:** Minimum length of the secrets to generate (1-10 characters recommended for browser-based attacks).
    4.  **Max Length:** Maximum length of the secrets to generate (1-10 characters recommended).
    5.  **Character Set:** Define the set of characters to use (e.g., `abcdefghijklmnopqrstuvwxyz0123456789`).

    **Note on Brute-Force:** Brute-forcing can be extremely time-consuming, especially for longer lengths or larger character sets. Browser-based brute-forcing is generally limited by JavaScript's performance and is best suited for short, simple keys or small character sets.

---

## Vulnerability Analysis

The "Potential Vulnerabilities" section provides immediate insights into common JWT weaknesses observed in the token's structure or claims:

* **`alg: none` Vulnerability:** Flags if the JWT is configured to use the "none" algorithm, which can bypass signature verification.
* **Algorithm Confusion (RS/HS):** Warns if an asymmetric algorithm (e.g., RS256) is used, as some implementations might be vulnerable to attacks where the public key is used as a symmetric key.
* **Missing `kid` (Key ID):** Highlights the absence of a `kid` header, which can complicate key rotation and management.
* **Expired/Not-Yet-Valid Token:** Checks the `exp` (expiration) and `nbf` (not before) claims to identify tokens that are no longer, or not yet, valid.
* **Missing `jti` (JWT ID):** Points out the lack of a unique JWT ID, which can make tokens susceptible to replay attacks if not handled by other means.
* **Insecure Header Parameters:** Identifies `jku` (JWK Set URL) or `x5u` (X.509 URL) parameters using non-HTTPS URLs, which could expose key retrieval to MITM attacks.

---

## Technology Stack

* **Frontend:**
    * HTML5
    * CSS3
    * JavaScript (ES6+)
    * [CryptoJS](https://cryptojs.gitbook.io/docs/) for HMAC hashing.
    * [jsrsasign](https://kjur.github.io/jsrsasign/) for JWT handling.
* **Backend (Minimal):**
    * PHP (for serving `index.php` and `weak_secrets.txt`)

---

## Contributing

Contributions are welcome! If you have suggestions for improvements, new features, or bug fixes, feel free to open an issue or submit a pull request.

1.  **Fork** the repository.
2.  **Create** a new branch (`git checkout -b feature/YourFeature`).
3.  **Commit** your changes (`git commit -m 'Add Your Feature'`).
4.  **Push** to the branch (`git push origin feature/YourFeature`).
5.  **Open** a Pull Request.

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## Contact

Feel free to reach out via Telegram: [![Telegram](https://img.shields.io/badge/Telegram-@aamershah.t.me-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/aamershah)

---
