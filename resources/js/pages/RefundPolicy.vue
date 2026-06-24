<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    document.title = 'Refund Policy - CA Graphic DTF Transfers';

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

    updateMeta('description', 'Refund Policy for CA Graphic DTF Transfers. Learn about our procedures regarding returns, refunds, replacements, and exchanges.');
    updateMeta('keywords', 'refund policy, returns, exchanges, DTF transfers, CA Graphic');
    updateMeta('og:title', 'Refund Policy - CA Graphic DTF Transfers', true);
    updateMeta('og:description', 'Refund Policy for CA Graphic DTF Transfers. Learn about our procedures regarding returns, refunds, replacements, and exchanges.', true);
});
</script>

<template>
    <div class="refund-policy-page">

        <!-- Main Content -->
        <section class="refund-content">
            <div class="refund-container">
                <!-- Introduction -->
                <div class="refund-section intro-section">
                    <h2 class="section-title">Refund Policy</h2>
                    <p class="intro-text">
                        *Effective Date: January 31, 2016*
                    </p>
                    <p class="intro-text">
                        Thank you for choosing CA Graphic DTF Transfers for your Direct-to-Film (DTF)
                        printing needs. We are committed to providing high-quality products, exceptional
                        service, and the best possible customer experience.
                    </p>
                    <p class="intro-text">
                        This Return and Refund Policy outlines our procedures regarding returns,
                        refunds, replacements, and exchanges to ensure transparency and customer
                        satisfaction. By placing an order with CA Graphic DTF Transfers or using our
                        services, you acknowledge and agree to the terms and conditions outlined in
                        this policy.
                    </p>

                </div>

                <div class="refund-section intro-section">
                    <h2 class="section-title">Refund Eligibility for Film Services:</h2>
                    <p class="intro-text">
                        *1. Satisfaction Guarantee1.1 Satisfaction Guarantee:
                    </p>
                    <p class="intro-text">
                        At CA Graphic DTF Transfers, we are committed to delivering high-quality DTF
                        prints and exceptional customer service. If you are not satised with your order,
                        please contact our Customer Support team within 3 business days of receiving
                        your order. We will review the issue and work with you to determine an
                        appropriate resolution.
                    </p>
                    <p class="intro-text">
                        2. Production Errors1.2 Service Errors:
                    </p>
                    <p class="intro-text">
                        If CA Graphic DTF Transfers makes an error during the production process that
                        signicantly affects the quality, accuracy, or usability of your order, you may be
                        eligible for a full refund, replacement, or complimentary reprint, as determined
                        by our team after reviewing the issue.
                    </p>
                    <p class="intro-text">
                        To ensure a timely resolution, customers may be required to provide
                        photographs or other documentation demonstrating the problem.
                    </p>
                </div>


                <div class="refund-section intro-section">
                    <h2 class="section-title">2. Refund Request Process</h2>
                    <p class="intro-text">
                        2.1 Notication: <br>
                        To initiate a refund, replacement, or return request, please contact our
                        Customer Support team at contact@cagraphicdtftransfers.com within 3
                        business days of receiving your order. Please include your order number,
                        contact information, and a detailed explanation of the issue.
                    </p>
                    <p class="intro-text">
                        2.2 Evaluation: <br>
                        Upon receiving your request, our team will review the information provided and
                        may request additional documentation, such as photographs or videos, to help
                        evaluate the issue. We may also discuss possible solutions with you before
                        determining the appropriate resolution.

                    </p>
                    <p class="intro-text">
                        2.3 Resolution Options: <br>
                        Depending on the nature of the issue and the results of our review, CA Graphic
                        DTF Transfers may offer one of the following remedies:

                    </p>
                    <ul class="bullet-list">
                        <li>Full refund</li>
                        <li>Partial refund</li>
                        <li>Complimentary reprint or replacement</li>
                        <li>Store credit or alternative compensation, when appropriate</li>
                    </ul>

                    <p class="intro-text">
                        All resolutions are subject to review and approval by our Customer Support
                        team.
                    </p>

                </div>

                <!-- Non-Refundable Situations -->
                <div class="refund-section">
                    <h2 class="section-title">Non-Refundable Situations:</h2>
                    
                    <h3 class="subsection-title">3.1 Non-Refundable Situations</h3>
                    <p class="intro-text">
                        The following situations are non-refundable. Products ordered based on customer specifications cannot be refunded. CA Graphic DTF Transfers does not offer refunds, returns, or exchanges based on change of mind after production.
                    </p>

                    <h3 class="subsection-title">3.2 Change of Mind</h3>
                    <p class="intro-text">
                        At CA Graphic DTF Transfers, we understand that sometimes customer preferences may change. However, if you change your mind about an order that has been placed and approved for production, we cannot provide a refund or credit.
                    </p>

                    <h3 class="subsection-title">3.3 Customer Responsibility</h3>
                    <p class="intro-text">
                        CA Graphic DTF Transfers is not responsible for reviewing and verifying all artwork, sizing, quantities, shipping information, and order details before submitting an order. CA Graphic DTF Transfers is not responsible for issues arising from incorrect information provided by the customer during the ordering process.
                    </p>
                    <p class="intro-text">
                        While we do our best not to be available in these situations, our team is committed to providing excellent customer service and will work with you to identify a fair and reasonable solution when possible. Your satisfaction is important to us, and we strive to make things right.
                    </p>
                </div>

                <!-- Exchanges -->
                <div class="refund-section">
                    <h2 class="section-title">Exchanges:</h2>
                    
                    <h3 class="subsection-title">4. Exchanges and Replacements</h3>
                    <p class="intro-text">
                        For customers with Custom Products: If you've received a defective or damaged custom printed product, CA Graphic DTF Transfers does not offer traditional exchanges.
                    </p>

                    <h3 class="subsection-title">4.1 Exchanges for Pre-Made Products</h3>
                    <p class="intro-text">
                        If there is a defect in the pre-made or pre-designed product purchased from CA Graphic DTF Transfers, customers may request a replacement at no cost. To arrange an exchange, contact our Customer Support team within the required notification period. We will review the issue and, when appropriate, provide a replacement, repair, or refund, as determined by our team.
                    </p>
                </div>

                <!-- Contact Information -->
                <div class="refund-section">
                    <h2 class="section-title">Contact Information:</h2>
                    
                    <p class="intro-text">
                        If you have any questions, concerns, or would like to submit a refund, replacement, or return request, please contact our Customer Support team:
                    </p>
                    
                    <div class="contact-info">
                        <strong>Email:</strong> <a href="mailto:contact@cagraphicdtftransfers.com">contact@cagraphicdtftransfers.com</a><br>
                        <strong>Phone:</strong> (312) 843-4099
                    </div>

                    <p class="intro-text">
                        We are committed to providing prompt assistance and resolving any issues as quickly as possible.
                    </p>
                </div>

                <!-- Modifications to the Policy -->
                <div class="refund-section contact-section">
                    <h2 class="section-title">Modifications to the Policy:</h2>
                    
                    <p class="intro-text">
                        CA Graphic DTF Transfers reserves the right to update, review, or modify this Refund Policy at any time. Any changes will be effective immediately upon posting to www.cagraphicdtftransfers.com.
                    </p>
                    <p class="intro-text">
                        We encourage you to review this policy periodically to stay informed of any updates. Your continued use of our website, products, or services after any posted modification constitutes your acceptance of the revised policy.
                    </p>
                    <p class="intro-text">
                        By using our services, you acknowledge that you have read, understood, and agree to the terms and conditions outlined in this Return and Refund Policy.
                    </p>

                    <h3 class="subsection-title">Contact Information</h3>
                    <p class="intro-text">
                        <strong>CA Graphic DTF Transfers</strong><br>
                        Chicago, IL 60608<br>
                        United States<br><br>
                        <strong>Phone:</strong> (312) 843-4099<br>
                        <strong>Email:</strong> <a href="mailto:contact@cagraphicdtftransfers.com">contact@cagraphicdtftransfers.com</a>
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.refund-policy-page {
    background: #ffffff;
}

/* Hero Section */
.refund-hero {
    padding: 60px 40px;
    text-align: center;
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
}

.refund-hero h1 {
    font-size: 48px;
    font-weight: 700;
    color: #1a1a1a;
}

/* Main Content */
.refund-content {
    padding: 60px 40px;
    background: #ffffff;
}

.refund-content {
    max-width: 900px;
    margin: 0 auto;
}

.refund-section {
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
    .refund-hero {
        padding: 40px 20px;
    }

    .refund-hero h1 {
        font-size: 32px;
    }

    .refund-content {
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
    .refund-hero {
        padding: 30px 16px;
    }

    .refund-hero h1 {
        font-size: 24px;
    }

    .refund-content {
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
