<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    document.title = 'Privacy Policy | CA Graphic DTF Transfers';

    const updateMeta = (name, content, isProperty = false) => {
        const selector = isProperty ? `meta[property="${name}"]` : `meta[name="${name}"]`;
        let meta = document.querySelector(selector);
        if (meta) meta.remove();

        const newMeta = document.createElement('meta');
        if (isProperty) {
            newMeta.setAttribute('property', name);
        } else {
            newMeta.setAttribute('name', name);
        }
        newMeta.content = content;
        document.head.appendChild(newMeta);
    };

    updateMeta('description', 'Privacy Policy for CA Graphic DTF Transfers. Learn how we protect your personal information and data.');
    updateMeta('keywords', 'privacy policy, data protection, DTF transfers, CA Graphic');
    updateMeta('og:title', 'Privacy Policy | CA Graphic DTF Transfers', true);
    updateMeta('og:description', 'Privacy Policy for CA Graphic DTF Transfers. Learn how we protect your personal information and data.', true);
});
</script>

<template>
    <div class="privacy-policy-page">

        <!-- Main Content -->
        <section class="privacy-content">
            <div class="policy-container">
                <!-- Introduction -->
                <div class="policy-section intro-section">
                    <h2 class="section-title">Privacy Policy</h2>
                    <p class="intro-text">
                        DTF Illinois (CA Graphic DTF Transfers) is committed to protecting your privacy while providing
                        high-quality services and ensuring a safe, secure, and enjoyable experience on the CA Graphic
                        DTF Transfers website at www.cagraphicdtftransfers.com and governs the collection, use, and
                        protection of information gathered through this website. By accessing or using the CA Graphic
                        DTF Transfers website, you acknowledge and consent to the data practices described in this
                        Statement.
                    </p>
                </div>

                <!-- Information We Collect -->
                <div class="policy-section">
                    <h2 class="section-title">Information We Collect</h2>
                    <p>
                        CA Graphic DTF Transfers may collect personally identifiable information (PII) when you use our
                        website, including your name, email address, mailing address, phone number, and payment
                        information. This information may be collected when you create an account, save a design, submit
                        artwork, or place an order.
                    </p>
                    <p>
                        We may also collect non-personally identifiable information, such as general geographic location
                        and demographic data.
                    </p>
                    <p>
                        In addition, CA Graphic DTF Transfers may automatically collect certain information about your
                        device or browsing activity. This information may include your IP address, browser type, website
                        addresses, operating system, website activity, saved designs, templates created, and other
                        similar technical data. This information helps us improve our website, improve our services,
                        maintain quality, and analyze website usage trends.
                    </p>
                </div>

                <!-- Third-Party Websites -->
                <div class="policy-section">
                    <h2 class="section-title">Third-Party Websites</h2>
                    <p>
                        The CA Graphic DTF Transfers website may contain links to third-party websites. We encourage you
                        to review the privacy policies of any website you visit, as their privacy practices may differ
                        from ours.
                    </p>
                    <p>
                        CA Graphic DTF Transfers is not responsible for the privacy practices, content, or policies of
                        any third-party websites linked to or from our website.
                    </p>
                </div>

                <!-- Use of Information About You -->
                <div class="policy-section">
                    <h2 class="section-title">Use of Information about You</h2>
                    <p>
                        CA Graphic DTF Transfers collects and uses information about you, including personally
                        identifiable information (PII), and information regarding your use of the CA Graphic DTF
                        Transfers website to operate our website, provide requested services, and improve our overall
                        customer experience.
                    </p>
                    <p>
                        We may use your information to send you promotional information about products, services,
                        promotions, or updates offered by CA Graphic DTF Transfers and its affiliates. We may also
                        contact you to participate in surveys or research requests regarding our current service or
                        potential new offerings. You may opt out of receiving promotional communications, surveys, or
                        research requests at any time by using the unsubscribe link included in our emails.
                    </p>
                    <p>
                        CA Graphic DTF Transfers may share information with our business partners regarding products or
                        services that may be of interest to you. In addition, we may work with third-party service
                        providers to deliver personalized product recommendations and advertising. These providers may
                        use information collected through the website to help present products and services that are
                        relevant to your interests.
                    </p>
                    <p>
                        To learn more about opting out of interest-based advertising, please visit
                        www.networkadvertising.org. You may also contact us directly for assistance regarding
                        advertising preferences.
                    </p>
                    <p>
                        CA Graphic DTF Transfers may share information, including PII, with trusted third-party partners
                        who assist us with services such as data analysis, email communications, customer support,
                        payment processing, and order fulfillment. These partners are contractually obligated to use
                        your information only as necessary to provide services on behalf of CA Graphic DTF Transfers.
                    </p>
                    <p>
                        We may track website usage, including pages visited and customer interactions within the CA
                        Graphic DTF Transfers website, to better understand customer preferences and improve our
                        products, services, content, and advertising. This information helps us deliver a customized
                        experience to our users.
                    </p>
                    <p>
                        CA Graphic DTF Transfers will not use or disclose sensitive personal information, such as
                        information related to race, religion, or political beliefs, without your express consent.
                    </p>
                    <p>
                        We do not collect personally identifiable information for the purpose of tracking users across
                        third-party websites over time. Therefore, our website does not specifically respond to browser
                        "Do Not Track" signals.
                    </p>
                </div>

                <!-- Google Analytics -->
                <div class="policy-section">
                    <h2 class="section-title">Google Analytics</h2>
                    <p>
                        CA Graphic DTF Transfers uses Google Analytics to collect and process certain website analytics
                        data. For information about how Google collects and processes data, please visit Google's
                        Privacy & Terms resources.
                    </p>
                </div>

                <!-- Legal Disclosure -->
                <div class="policy-section">
                    <h2 class="section-title">Legal Disclosure</h2>
                    <p>
                        CA Graphic DTF Transfers may disclose your personally identifiable information without prior
                        notice if required by law or if we believes, in good faith, that such action is necessary to:
                        (a) Comply with applicable laws, regulations, legal process, or governmental requests; (b)
                        Protect and defend the rights, property, or operations of CA Graphic DTF Transfers; (c)
                        Investigate or prevent fraud; or (d) Protect the personal safety of our customers, employees,
                        website users, or the public.
                    </p>
                </div>

                <!-- Cookies and Tracking Technologies -->
                <div class="policy-section">
                    <h2 class="section-title">Cookies and Tracking Technologies</h2>
                    <p>
                        The CA Graphic DTF Transfers website may use cookies and similar technologies to enhance and
                        personalize your online experience. A cookie is an email that is stored on your device by a web
                        server. Cookies cannot be used to run programs or deliver viruses to your computer. Each cookie
                        is uniquely designed for you and can only be read by the web server that issued it.
                    </p>
                    <p>
                        One of the primary reasons we use cookies is to help us personalize your preferences and improve
                        your browsing experience. Cookies also allow our website to recognize when you return and help
                        save your time by remembering your preferences and account information.
                    </p>
                    <p>
                        For example, if you register on an account, save designs, or place an order through the CA
                        Graphic DTF Transfers website, cookies may be used to remember certain information, login
                        information, and other settings. This allows you to access customized features and complete
                        future transactions more efficiently.
                    </p>
                    <p>
                        You have the option to accept or decline cookies. Most web browsers automatically accept
                        cookies; however, you can modify your browser settings to refuse cookies if you prefer. Please
                        note that disabling cookies may limit your ability to use and the functionality of the CA
                        Graphic DTF Transfers website and related services.
                    </p>
                </div>

                <!-- CCPA Notice -->
                <div class="policy-section">
                    <h2 class="section-title">California Consumer Privacy Act (CCPA) Notice</h2>
                    <p>
                        CA Graphic DTF Transfers does not sell personal information. However, we may collect certain
                        personal information from California residents solely for legitimate business purposes, as
                        defined under the California Consumer Privacy Act (CCPA).
                    </p>

      
     

           
                </div>



                <div class="policy-section">
                    <h2 class="section-title">Personal Information We Collect</h2>
                    <p>We may collect the following categories of personal information:</p>
                    <ul class="bullet-list">
                        <li>Name</li>
                        <li>Physical Address</li>
                        <li>Email Address</li>
                        <li>Phone Number</li>
                        <li>IP Address</li>
                        <li>Website Usage and Behavioral Data</li>
                    </ul>
                </div>



                <div class="policy-section">
                    <h2 class="section-title">Business Purposes for Collection and Use</h2>
                    <p>The personal information listed above may be collected and used for the following business
                        purposes:</p>
                    <ul class="bullet-list">
                        <li>Processing and fulfilling orders</li>
                        <li>Providing customer support</li>
                        <li>Improving our website and services</li>
                        <li>Marketing and promoting CA Graphic DTF Transfers products and services</li>
                    </ul>
                    <p>
                        Any third-party service providers that assist us in carrying out these business purposes are
                        contractually required to use personal information only as necessary to provide services on
                        behalf of CA Graphic DTF Transfers and to maintain the confidentiality and security of such
                        information.
                    </p>
                </div>


     <div class="policy-section">

    <h2 class="section-title">Your Right to Request Deletion</h2>
                    <p>
                        Under the CCPA, California residents have the right to request the deletion of personal
                        information that we have collected and retained, subject to certain legal exceptions.
                    </p>
                    <p>
                        Upon receiving and verifying your request, we will delete your personal information from our
                        records and instruct our service providers to do the same, unless an applicable exception
                        permits us to retain the information.
                    </p>

                </div>
                <div class="policy-section">

     

                    <h2  class="section-title">Exercising Your CCPA Rights</h2>
                    <p>To submit a request regarding your personal information, please contact us:</p>
                    <p class="contact-info">
                        <strong>Phone:</strong> 1-800-273-4644E<br>
                        <strong>Email:</strong> <a
                            href="mailto:contact@cagraphicdtftransfers.com">contact@cagraphicdtftransfers.com</a>
                    </p>
                    <p>We will respond to verifiable consumer requests in accordance with applicable California law.</p>

                </div>




                <!-- Security of Your Personal Information -->
                <div class="policy-section">
                    <h2 class="section-title">Security of Your Personal Information</h2>
                    <p>
                        CA Graphic DTF Transfers takes reasonable measures to protect your personally identifiable
                        information (PII) against unauthorized access, use, disclosure, alteration, or destruction.
                    </p>
                    <p>
                        We store personal information on secure computer systems located in controlled environments and
                        implement appropriate administrative, technical, and physical safeguards to protect your
                        information. Access to personal information is limited to authorized personnel who have a
                        need-to-know basis.
                    </p>
                    <p>
                        When sensitive information, such as payment details, is transmitted through our website, we use
                        industry-standard encryption technologies, including Secure Socket Layer (SSL) protocols, to
                        help safeguard your information during transmission.
                    </p>
                    <p>
                        CA Graphic DTF Transfers does not store or retain your full credit card information without
                        first providing notice and obtaining your express consent, where applicable.
                    </p>
                    <p>
                        While we strive to use commercially acceptable means to protect your personal information, no
                        method of transmission over the Internet or electronic storage system is completely secure.
                        Therefore, we cannot guarantee the absolute security of any information transmitted through our
                        website.
                    </p>
                    <p>
                        Any transmission of personal information is made at your own risk. CA Graphic DTF Transfers is
                        not responsible for the circumvention of any privacy settings, security measures, or safeguards
                        contained on or related to our website.
                    </p>
                </div>

                <!-- Changes to This Privacy Statement -->
                <div class="policy-section">
                    <h2 class="section-title">Changes to This Privacy Statement</h2>
                    <p>
                        CA Graphic DTF Transfers may update this Privacy Statement from time to time to reflect changes
                        in our business practices, new technologies, applicable laws and regulations.
                    </p>
                    <p>
                        We encourage you to review this Privacy Statement periodically to stay informed about how we
                        collect, use, and protect your information.
                    </p>
                    <p>
                        If we make changes to this Privacy Statement, we will post the updated version on our website
                        and revise the "Last Updated" date accordingly. If we changes materially impact your privacy
                        rights, we may notify you. Such notice may be provided by email. If we have your email address
                        on file, through a website notification, or by other appropriate means.
                    </p>
                    <p>
                        Your continued use of CA Graphic DTF Transfers website after any updates become effective
                        constitutes your acknowledgment that you have read, understood, and agreed to the revised
                        Privacy Statement.
                    </p>
                </div>

                <!-- Contact Us -->
                <div class="policy-section contact-section">
                    <h2 class="section-title">Contact Us</h2>
                    <p>
                        CA Graphic DTF Transfers welcomes your questions, comments, and feedback regarding this Privacy
                        Statement.
                    </p>
                    <p>
                        If you believe that CA Graphic DTF Transfers has not complied with the terms of this Privacy
                        Statement, please contact us using our inquiry form, and we will make every reasonable effort to
                        promptly investigate and address your concerns.
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.privacy-policy-page {
    background: #ffffff;
}

/* Hero Section */
.privacy-hero {
    padding: 60px 40px;
    text-align: center;
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
}

.privacy-hero h1 {
    font-size: 48px;
    font-weight: 700;
    color: #1a1a1a;
}

/* Main Content */
.privacy-content {
    padding: 60px 40px;
    background: #ffffff;
}

.policy-container {
    max-width: 900px;
    margin: 0 auto;
}

.policy-section {
    margin-bottom: 50px;
}

.intro-section {
    margin-bottom: 50px;
}

.intro-text {
    font-size: 15px;
    color: #333;
    line-height: 1.8;
}

.section-title {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 20px;
    margin-top: 30px;
    text-align: center;
}

.subsection-title {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
    margin-top: 24px;
    margin-bottom: 14px;
}

.policy-section p {
    font-size: 15px;
    color: #333;
    margin-bottom: 14px;
    line-height: 1.8;
}

.bullet-list {
    list-style: disc;
    margin-left: 24px;
    margin-bottom: 16px;
}

.bullet-list li {
    font-size: 15px;
    color: #333;
    margin-bottom: 8px;
    line-height: 1.6;
}

.contact-info {
    margin: 16px 0;
    font-size: 15px;
    color: #333;
}

.contact-info a {
    color: #0099ff;
    text-decoration: none;
}

.contact-info a:hover {
    text-decoration: underline;
}

.contact-section {
    margin-top: 50px;
    padding-top: 40px;
    border-top: 1px solid #e0e0e0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .privacy-hero {
        padding: 40px 20px;
    }

    .privacy-hero h1 {
        font-size: 32px;
    }

    .privacy-content {
        padding: 40px 20px;
    }

    .section-title {
        font-size: 24px;
    }

    .subsection-title {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .privacy-hero {
        padding: 30px 16px;
    }

    .privacy-hero h1 {
        font-size: 24px;
    }

    .privacy-content {
        padding: 30px 16px;
    }

    .section-title {
        font-size: 20px;
    }

    .subsection-title {
        font-size: 15px;
    }

    .policy-section p {
        font-size: 14px;
    }

    .bullet-list {
        margin-left: 20px;
    }

    .bullet-list li {
        font-size: 14px;
        margin-bottom: 6px;
    }
}
</style>
