<section class="min-h-screen flex items-center bg-seraph-ivory py-32 px-6">
    <div class="max-w-md w-full mx-auto">
        
        <!-- Login Card -->
        <div class="bg-white p-8 md:p-12 border border-seraph-charcoal/5 shadow-lg reveal-up">
            
            <div class="text-center mb-10">
                <h1 class="font-display text-3xl text-seraph-charcoal mb-2">Sign In</h1>
                <p class="font-body text-seraph-slate">Enter your email to continue</p>
            </div>

            <!-- Step 1: Email Input -->
            <div id="emailStep">
                <form id="emailForm" class="space-y-6">
                    <div>
                        <label for="email" class="block font-body text-sm font-medium text-seraph-charcoal mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required
                               placeholder="you@example.com"
                               class="w-full px-4 py-4 bg-seraph-cream/30 border border-seraph-charcoal/10 focus:border-seraph-amber focus:outline-none transition-colors font-body">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="rememberMe" name="remember_me"
                               class="w-4 h-4 text-seraph-amber border-seraph-charcoal/20 rounded focus:ring-seraph-amber">
                        <label for="rememberMe" class="ml-2 font-body text-sm text-seraph-slate">Remember me for 30 days</label>
                    </div>

                    <div id="emailMessage" class="min-h-[24px] text-sm font-body text-center"></div>

                    <button type="submit" id="emailBtn" class="w-full btn-primary py-4 font-medium tracking-wide">
                        Continue
                    </button>
                </form>
            </div>

            <!-- Step 2: Verification Code -->
            <div id="codeStep" class="hidden">
                <div class="mb-6 p-4 bg-seraph-cream/50 border border-seraph-amber/20 rounded">
                    <p class="font-body text-sm text-seraph-slate">
                        We sent a verification code to <strong id="displayEmail" class="text-seraph-charcoal"></strong>
                    </p>
                </div>
                
                <form id="codeForm" class="space-y-6">
                    <div>
                        <label for="code" class="block font-body text-sm font-medium text-seraph-charcoal mb-2">Verification Code</label>
                        <input type="text" id="code" name="code" required
                               placeholder="Enter 6-digit code"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               class="w-full px-4 py-4 bg-seraph-cream/30 border border-seraph-charcoal/10 focus:border-seraph-amber focus:outline-none transition-colors font-body text-center text-2xl tracking-[0.3em]">
                    </div>

                    <div id="codeMessage" class="min-h-[24px] text-sm font-body text-center"></div>

                    <button type="submit" id="codeBtn" class="w-full btn-primary py-4 font-medium tracking-wide">
                        Verify & Sign In
                    </button>
                </form>
                
                <button type="button" id="backBtn" class="w-full mt-4 py-3 font-body text-sm text-seraph-slate hover:text-seraph-charcoal transition-colors">
                    ← Use a different email
                </button>
            </div>
            
            <!-- Step 3: Name Input (for new users) -->
            <div id="nameStep" class="hidden">
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded">
                    <p class="font-body text-sm text-green-700">
                        ✓ Email verified! Complete your profile to continue.
                    </p>
                </div>
                
                <form id="nameForm" class="space-y-6">
                    <div>
                        <label for="name" class="block font-body text-sm font-medium text-seraph-charcoal mb-2">Your Name</label>
                        <input type="text" id="name" name="name" required
                               placeholder="Enter your full name"
                               class="w-full px-4 py-4 bg-seraph-cream/30 border border-seraph-charcoal/10 focus:border-seraph-amber focus:outline-none transition-colors font-body">
                    </div>

                    <div id="nameMessage" class="min-h-[24px] text-sm font-body text-center"></div>

                    <button type="submit" id="nameBtn" class="w-full btn-primary py-4 font-medium tracking-wide">
                        Complete Sign Up
                    </button>
                </form>
            </div>

        </div>
        
    </div>
</section>

<script>
    const baseUrl = '<?php echo BASE_URL; ?>';
    let userEmail = '';
    let userCode = '';
    let rememberMe = false;

    // Elements
    const emailStep = document.getElementById('emailStep');
    const codeStep = document.getElementById('codeStep');
    const nameStep = document.getElementById('nameStep');
    
    // Step 1: Send verification code
    document.getElementById('emailForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        rememberMe = document.getElementById('rememberMe').checked;
        const btn = document.getElementById('emailBtn');
        const msg = document.getElementById('emailMessage');

        btn.innerText = 'Sending code...';
        btn.disabled = true;
        msg.innerText = '';

        try {
            const response = await fetch(baseUrl + 'api/auth/send-code', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            });

            const data = await response.json();

            if (data.success) {
                userEmail = email;
                document.getElementById('displayEmail').innerText = email;
                emailStep.classList.add('hidden');
                codeStep.classList.remove('hidden');
                document.getElementById('code').focus();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            msg.className = 'min-h-[24px] text-sm font-body text-center text-red-600';
            msg.innerText = error.message;
        } finally {
            btn.innerText = 'Continue';
            btn.disabled = false;
        }
    });
    
    // Step 2: Verify code
    document.getElementById('codeForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const code = document.getElementById('code').value.trim();
        const btn = document.getElementById('codeBtn');
        const msg = document.getElementById('codeMessage');

        btn.innerText = 'Verifying...';
        btn.disabled = true;
        msg.innerText = '';

        try {
            const response = await fetch(baseUrl + 'api/auth/verify-code', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: userEmail, code, remember_me: rememberMe })
            });

            const data = await response.json();

            if (data.success) {
                // Redirect to intended page or products
                const redirect = '<?php echo $_SESSION['redirect_after_login'] ?? BASE_URL . 'products'; ?>';
                <?php unset($_SESSION['redirect_after_login']); ?>
                window.location.href = redirect;
            } else if (data.needsName) {
                // New user - need name
                userCode = code;
                codeStep.classList.add('hidden');
                nameStep.classList.remove('hidden');
                document.getElementById('name').focus();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            msg.className = 'min-h-[24px] text-sm font-body text-center text-red-600';
            msg.innerText = error.message;
        } finally {
            btn.innerText = 'Verify & Sign In';
            btn.disabled = false;
        }
    });
    
    // Step 3: Submit name (new users)
    document.getElementById('nameForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const btn = document.getElementById('nameBtn');
        const msg = document.getElementById('nameMessage');

        btn.innerText = 'Creating account...';
        btn.disabled = true;
        msg.innerText = '';

        try {
            const response = await fetch(baseUrl + 'api/auth/verify-code', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: userEmail, code: userCode, name, remember_me: rememberMe })
            });

            const data = await response.json();

            if (data.success) {
                const redirect = '<?php echo $_SESSION['redirect_after_login'] ?? BASE_URL . 'products'; ?>';
                window.location.href = redirect;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            msg.className = 'min-h-[24px] text-sm font-body text-center text-red-600';
            msg.innerText = error.message;
        } finally {
            btn.innerText = 'Complete Sign Up';
            btn.disabled = false;
        }
    });
    
    // Back button
    document.getElementById('backBtn').addEventListener('click', () => {
        codeStep.classList.add('hidden');
        emailStep.classList.remove('hidden');
        document.getElementById('code').value = '';
        document.getElementById('email').focus();
    });
</script>
