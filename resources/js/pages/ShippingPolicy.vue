<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    document.title = 'Shipping Policy - CA Graphic DTF Transfers';

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

    updateMeta('description', 'Shipping Policy for CA Graphic DTF Transfers. Learn about our procedures regarding shipping, delivery times, and related policies.');
    updateMeta('keywords', 'shipping policy, delivery, DTF transfers, CA Graphic');
    updateMeta('og:title', 'Shipping Policy - CA Graphic DTF Transfers', true);
    updateMeta('og:description', 'Shipping Policy for CA Graphic DTF Transfers. Learn about our procedures regarding shipping, delivery times, and related policies.', true);
});
</script>

<template>
    <div class="shipping-policy-page">

        <!-- Main Content -->
        <section class="shipping-content">
            <div class="shipping-container">
                <!-- Introduction -->
                <div class="shipping-section intro-section">
                    <h2 class="section-title">Shipping Policy</h2>
                    <p class="intro-text">
                        Our turnaround time may vary depending on the size of your order, current
                        production volume, and the quality of the artwork les provided. In most cases,
                        orders are processed within next business days (terms and conditions apply).
                    </p>
                    <p class="intro-text">
                        If you have a rush order or an upcoming deadline, we're here to help. Please
                        contact our Customer Support team at contact@cagraphicdtftransfers.com
                        or call/text (312) 843-4099, and we will do our best to accommodate your needs.
                    </p>



                    <p class="intro-text text-bold">
                        Free Shipping
                    </p>
                    <p class="intro-text">
                        We proudly offer FREE shipping on orders over $170.
                    </p>
                    <p class="intro-text text-bold">
                        Order Processing
                    </p>
                    <p class="intro-text">
                        Our goal is to ship all orders within 24 hours after artwork approval and order
                        nalization. However, certain orders may require additional production time due
                        to order complexity, artwork issues, or missing information.
                    </p>
                    <p class="intro-text text-bold">
                        Weekend & Holiday Orders
                    </p>
                    <p class="intro-text ">
                        Orders may be placed 24/7, including weekends and public holidays. Orders
                        submitted outside of regular business days will be processed and shipped on
                        the next available business day.
                    </p>
                    <p class="intro-text text-bold">
                        Estimated Delivery Times
                    </p>
                    <p class="intro-text">
                        United States Delivery:
                    </p>




                    <ul class="bullet-list">
                        <li>Estimated delivery time: 1–3 business days after shipment</li>
                        <li>Delivery times may vary depending on carrier operations, weather conditions, and destination
                        </li>
                    </ul>



                    <p class="intro-text">
                        If you have any questions regarding production, shipping, or delivery, please
                        contact our friendly Customer Support team. We're always happy to assist you
                        and ensure your order arrives as quickly as possible.
                    </p>



                </div>


            </div>
        </section>
    </div>
</template>

<style scoped>
.shipping-policy-page {
    background: #ffffff;
}

/* Hero Section */
.shipping-hero {
    padding: 60px 40px;
    text-align: center;
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
}

.shipping-hero h1 {
    font-size: 48px;
    font-weight: 700;
    color: #1a1a1a;
}

/* Main Content */
.shipping-content {
    padding: 60px 40px;
    background: #ffffff;
}

.shipping-content {
    max-width: 900px;
    margin: 0 auto;
}

.shipping-section {
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

.text-bold {
    font-weight: bold;

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

.shipping-section p {
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
    .shipping-hero {
        padding: 40px 20px;
    }

    .shipping-hero h1 {
        font-size: 32px;
    }

    .shipping-content {
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
    .shipping-hero {
        padding: 30px 16px;
    }

    .shipping-hero h1 {
        font-size: 24px;
    }

    .shipping-content {
        padding: 30px 16px;
    }

    .section-title {
        font-size: 20px;
    }

    .subsection-title {
        font-size: 15px;
    }

    .shipping-section p {
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
