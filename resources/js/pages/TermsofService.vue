<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    document.title = 'Terms of Service | CA Graphic DTF Transfers';

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

    updateMeta('description', 'Terms of Service for CA Graphic DTF Transfers. Learn about our policies, user responsibilities, and legal agreements.');
    updateMeta('keywords', 'terms of service, user agreement, DTF transfers, CA Graphic');
    updateMeta('og:title', 'Terms of Service | CA Graphic DTF Transfers', true);
    updateMeta('og:description', 'Terms of Service for CA Graphic DTF Transfers. Learn about our policies, user responsibilities, and legal agreements.', true);
});
</script>

<template>
    <div class="terms-of-service-page">

        <!-- Main Content -->
        <section class="terms-content">
            <div class="terms-container">
                <!-- Introduction -->
                <div class="terms-section intro-section">
                    <h2 class="section-title">Terms of Service</h2>
                    <p class="intro-text">
                        Effective Date: January 1, 2016
                    </p>
                    <p class="intro-text">
                        Welcome to the <strong>CA Graphic DTF Transfers</strong> website, located at
                        <strong>www.cagraphicdtftransfers.com</strong> (the "Website").
                    </p>



                    <p class="intro-text ">
                        These Terms of Service ("Terms" or "TOS") govern your access to and use of the
                        Website and the services provided by CA Graphic DTF Transfers (collectively,
                        the "Services"). By accessing or using this Website, you agree to be bound by
                        these Terms of Service and all applicable laws and regulations.
                    </p>
                    <p class="intro-text">
                        These Terms describe your rights and responsibilities as a user, as well as the
                        rights and responsibilities of CA Graphic DTF Transfers. If you do not agree to
                        these Terms, you should not access or use the Website or any of our Services.
                    </p>


                    <p class="intro-text">
                        <strong>CA Graphic DTF Transfers</strong> and its associated logos, trademarks, and branding
                        are the property of CA Graphic DTF Transfers and may not be used without
                        prior written permission.
                    </p>

                    <p class="intro-text text-bold">
                        Changes to These Terms
                    </p>

                    <p class="intro-text">
                        CA Graphic DTF Transfers reserves the right to update, revise, modify, or
                        replace these Terms of Service at any time and at its sole discretion. Any
                        changes will become effective immediately upon posting the updated Terms on
                        our Website.
                    </p>

                    <p class="intro-text">
                        It is your responsibility to review these Terms periodically for any updates. If
                        you do not agree with any modications, your sole remedy is to discontinue use
                        of the Website and Services.
                    </p>

                    <p class="intro-text">
                        Your continued use of the Website after any revised Terms have been posted
                        constitutes your acceptance of those changes and your agreement to be bound
                        by the updated Terms of Service.
                    </p>

                </div>


            </div>
            <div class="terms-container">

                <div class="terms-section intro-section">
                    <h2 class="section-title">1. The cagraphicdtftansfers.com Service</h2>
                    <p class="intro-text">
                        Services Provided
                    </p>

                    <p class="intro-text ">
                        CA Graphic DTF Transfers provides a variety of online services through its
                        website (collectively, the "Services"). These services allow customers to create,
                        customize, and order Direct-to-Film (DTF) transfers, apparel, promotional
                        products, and other custom-printed items.
                    </p>
                    <p class="intro-text">
                        Through our Services, customers may design and purchase custom products,
                        including but not limited to T-shirts, hoodies, sweatshirts, hats, tote bags, and
                        other apparel and merchandise (collectively, the "Products").
                    </p>


                    <p class="intro-text">
                        Products purchased through CA Graphic DTF Transfers are intended for
                        personal, business, promotional, or resale use, subject to these Terms of Service
                        and all applicable laws and regulations.
                    </p>


                </div>


            </div>
   

                <div class="terms-section intro-section">
                    <h2 class="section-title">2. Use of the Web Site and cagraphicdtftansfers.com Service</h2>
                    <p class="intro-text text-bold">
                        2. Eligibility, Compliance, and Use of Services
                    </p>

                    <p class="intro-text text-bold">
                        2.1 Eligibility
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers provides its Services only to individuals and entities
                        that can legally enter into binding contracts under applicable law.
                    </p>


                    <p class="intro-text">
                        If you are under the age of 18 but at least 13 years old, you may use the Website
                        and Services only under the supervision of a parent or legal guardian who
                        agrees to be bound by these Terms of Service. The Website and Services are
                        not intended for children under the age of 13.
                    </p>
                    <p class="intro-text text-bold">
                        2.2 Compliance with Terms and Applicable Law
                    </p>
                    <p class="intro-text">
                        By using the Website and Services, you agree to comply with these Terms of Service, all applicable policies and agreements referenced herein, and all applicable federal, state, and local laws, regulations, and rules.
                    </p>


                    <p class="intro-text text-bold">
                        2.3 Licensee to Use the Website and Services
                    </p>
                    
                    <p class="intro-text text-bold">
                        (a) Ownership Rights
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers exclusively owns or licenses all intellectual property rights, titles, and interests related to the Website and Services, except as otherwise expressly stated in these Terms. This includes, but is not limited to, trademarks, logos, website content, graphics, software, technology, and other proprietary materials used to operate the Services.
                    </p>
                    <p class="intro-text">
                        Nothing contained in these Terms grants you any ownership rights or interests in the Website, Services, or any intellectual property owned by CA Graphic DTF Transfers.
                    </p>

                    <p class="intro-text text-bold">
                        (b) Limited License
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers grants you a limited, non-exclusive, non-transferable, and revocable license to access and use the Website and Services solely for their intended purposes and in accordance with these Terms.
                    </p>
                    <p class="intro-text">
                        This license does not permit you to:
                    </p>
                    <ul class="bullet-list">
                        <li>Copy, reproduce, distribute, or modify Website content without authorization</li>
                        <li>Use Website content for commercial purposes unrelated to CA Graphic DTF Transfers</li>
                        <li>Collect or harvest information from the Website</li>
                        <li>Create derivative works based on Website content</li>
                        <li>Reverse engineer, decompiler, or attempt to gain unauthorized access to the Website or Services</li>
                    </ul>


                    <p class="intro-text text-bold">
                        2.4 Third-Party Services
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers may utilize third-party service providers to facilitate certain features or services available through the Website, including payment processing, shipping, analytics, marketing, and customer support.
                    </p>
                    <p class="intro-text">
                        These third-party providers operate independently and may have their own terms, conditions, and privacy policies. CA Graphic DTF Transfers does not control and is not responsible for the actions, services, or policies of any third-party provider.
                    </p>
                    <p class="intro-text">
                        By using such services, you agree to comply with any applicable third-party terms and policies. In the event of a conflict between a third-party policy and these Terms of Service, the provisions of these Terms of Service shall govern your use of the Website and Services.
                    </p>


                </div>


          

                <div class="terms-section intro-section">
                    <h2 class="section-title">3. General Rules</h2>
                    
                    <p class="intro-text text-bold">
                        3.1 Prohibited Use
                    </p>
                    <p class="intro-text">
                        You may use the CA Graphic DTF Transfers Website and Services only for lawful purposes and in accordance with these Terms of Service.
                    </p>
                    <p class="intro-text">
                        You agree not to:
                    </p>
                    <ul class="bullet-list">
                        <li>Interfere with, disrupt, or damage the Website, Services, servers, or networks through the use of viruses, malware, malicious code, or any other harmful technology.</li>
                        <li>Modify, adapt, create derivative works from, reverse engineer, decompile, disassemble, or attempt to access the source code of any software, technology, or systems used to provide the Services.</li>
                        <li>Use automated tools, including robots, spiders, scrapers, crawlers, or similar technologies, to access, monitor, copy, or collect information from the Website without prior written authorization, except for legitimate search engine indexing.</li>
                        <li>Collect, harvest, or store personal information, email addresses, or other data belonging to other users or visitors of the Website.</li>
                        <li>Impersonate any person, company, or entity, or falsely represent your affiliation with any individual or organization.</li>
                        <li>Use CA Graphic DTF Transfers' name, trademarks, logos, service marks, or branding in an unauthorized manner, including within metadata, keywords, advertising, domain names, or search engine optimization activities.</li>
                        <li>Engage in any activity that interferes with or restricts another user's ability to access or use the Website or Services.</li>
                        <li>Use the Website or Services for any unlawful, fraudulent, deceptive, abusive, or harmful purpose.</li>
                        <li>Assist, encourage, or enable any third party to engage in any activity prohibited by these Terms of Service.</li>
                    </ul>

                    <p class="intro-text text-bold">
                        3.2 Compliance with Prohibited Use Restrictions
                    </p>
                    <p class="intro-text">
                        By accessing or using the Website and Services, you represent and warrant that you will not use the Website or Services for any prohibited purpose described in these Terms.
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers reserves the right to suspend, restrict, or terminate access to the Website or Services for any user who violates these Terms of Service or engages in conduct that may harm the Website, Services, customers, or business operations.
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers also reserves the right to pursue any legal or equitable remedies available under applicable law for violations of these Terms.
                    </p>

                </div>




            
                <div class="terms-section intro-section">
                    <h2 class="section-title">4. Use of Your Content</h2>
                    
                    <p class="intro-text">
                        You retain ownership of all artwork, designs, images, logos, text, and other content ("Content") that you create, upload, or submit through the CA Graphic DTF Transfers Website and Services.
                    </p>

                    <p class="intro-text">
                        By uploading, submitting, or creating Content using the Website or any design tools provided by CA Graphic DTF Transfers, you grant CA Graphic DTF Transfers a non-exclusive, worldwide, transferable, sublicenseable, royalty-free license to use, reproduce, modify, display, print, distribute, and sell such Content solely for the purpose of:
                    </p>
                    <ul class="bullet-list">
                        <li>Producing and fulfilling your orders</li>
                        <li>Operating and providing the Services</li>
                        <li>Displaying product previews and mockups</li>
                        <li>Marketing, advertising, and promoting CA Graphic DTF Transfers and its products and services</li>
                    </ul>

                    <p class="intro-text">
                        You also grant CA Graphic DTF Transfers the right to make reasonable modifications to your Content when necessary for printing, production, formatting, sizing, color correction, or other technical requirements related to order fulfillment and promotional use.
                    </p>

                    <p class="intro-text">
                        You may remove or delete your Content from your account at any time. Removal of Content does not affect orders that have already been placed, processed, produced, or shipped.
                    </p>

                    <p class="intro-text">
                        You retain all copyrights, trademarks, and other intellectual property rights associated with your Content. If you choose to remove your Content and notify CA Graphic DTF Transfers that you wish to terminate the license granted above, we will make reasonable efforts to discontinue future use of the Content. However, CA Graphic DTF Transfers reserves the right to:
                    </p>
                    <ul class="bullet-list">
                        <li>Complete and fulfill any orders placed before receipt of your termination request</li>
                        <li>Maintain records required for business, legal, or operational purposes</li>
                        <li>Continue using previously produced marketing, advertising, promotional, portfolio, sample, or archival materials that were created prior to the termination request</li>
                    </ul>

                    <p class="intro-text">
                        Nothing in these Terms transfers ownership of your intellectual property to CA Graphic DTF Transfers.
                    </p>

                </div>

      

                <div class="terms-section intro-section">
                    <h2 class="section-title">5. Reservation of Rights</h2>
                    
                    <p class="intro-text text-bold">
                        5.1 Monitoring
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers reserves the right, but is not obligated, to monitor activity, communications, submissions, and transactions conducted through the Website and Services.
                    </p>
                    <p class="intro-text">
                        If CA Graphic DTF Transfers determines, in its sole discretion, that any user has violated these Terms of Service, applicable laws, or engaged in conduct that is inappropriate, harmful, fraudulent, offensive, or otherwise objectionable, we may take any action we deem necessary. Such actions may include, but are not limited to:
                    </p>
                    <ul class="bullet-list">
                        <li>Canceling orders or transactions</li>
                        <li>Removing content or materials</li>
                        <li>Restricting access to certain features or services</li>
                        <li>Suspending or terminating user accounts</li>
                        <li>Reporting unlawful activity to appropriate authorities</li>
                    </ul>
                    <p class="intro-text">
                        CA Graphic DTF Transfers reserves the right to take these actions without prior notice and without liability to you or any third party.
                    </p>

                    <p class="intro-text text-bold">
                        5.2 Modification of Services
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers reserves the right to modify, update, suspend, discontinue, or enhance any aspect of the Website or Services at any time, with or without prior notice.
                    </p>
                    <p class="intro-text">
                        We may make changes to features, functionality, pricing, products, policies, technology, or service offerings as part of our ongoing efforts to improve the customer experience and business operations.
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers shall not be liable to you or any third party for any modification, suspension, interruption, or discontinuation of any portion of the Website or Services.
                    </p>

                </div>


                <div class="terms-section intro-section">
                    <h2 class="section-title">6. Submissions</h2>
                    
                    <p class="intro-text">
                        When you submit questions, comments, suggestions, ideas, reviews, feedback, contest entries, communications, materials submitted through web forms, message board postings, or any other information (collectively, "Submissions"), you grant CA Graphic DTF Transfers a non-exclusive, worldwide, royalty-free, perpetual, irrevocable, transferable, and sublicensable right to use, reproduce, modify, publish, distribute, display, and otherwise utilize such Submissions for marketing, promotional, operational, and business purposes.
                    </p>
                    <p class="intro-text">
                        By submitting any Submission, you acknowledge and agree that CA Graphic DTF Transfers is under no obligation to treat such Submissions as confidential and may use them without compensation, attribution, or notice to you.
                    </p>
                    <p class="intro-text">
                        To the fullest extent permitted by law, you waive any claims based on moral rights, rights of attribution, or similar rights arising from CA Graphic DTF Transfers' use of your Submissions.
                    </p>
                    <p class="intro-text">
                        This section applies only to general feedback, comments, suggestions, reviews, communications, and other Submissions. It does not apply to your artwork, designs, logos, images, or other content submitted for printing, production, or inclusion in products ordered through the CA Graphic DTF Transfers Services. Such content is governed by the Intellectual Property and User Content provisions of these Terms of Service.
                    </p>

                </div>

    

                <div class="terms-section intro-section">
                    <h2 class="section-title">7. Representations and Warranties</h2>
                    
                    <p class="intro-text text-bold">
                        7.1 Mutual Representations and Warranties
                    </p>
                    <p class="intro-text">
                        You represent and warrant to DTF Illinois and DTF Illinois represents and warrants to you: (i) that you or it has the full power and authority to enter into and perform under these TOS; (ii) the execution and performance of your or its obligations under these TOS do not constitute a breach of or conflict with any other agreement or arrangement by which you or it is bound; and (iii) these TOS are a legal, valid and binding obligation of the party entering into the these TOS, enforceable in accordance with their terms and conditions.
                    </p>

                    <p class="intro-text text-bold">
                        7.2 By You
                    </p>
                    <p class="intro-text">
                        You represent and warrant to DTF Illinois that, your use of the cagraphicdtftransfers.com Service and any order that you place with DTF Illinois:
                    </p>
                    <p class="intro-text">
                        (i) will not infringe the copyright, trademark, patent, trade secret, right of privacy, right of publicity or any other legal right of any third party and (ii) will comply with all applicable laws, rules, and regulations. You further represent and warrant to DTF Illinois that: (i) there are no claims, demands or any form of litigation pending, or to the best of your knowledge, threatened with respect to any of your Content; (ii) DTF Illinois will not be required to make any payments to any third party in connection with its use of your Content, except for the expenses that DTF Illinois incurs in providing the cagraphicdtftransfers.com Service; (iii) the use of any instructions, formulae, recommendations, or the like contained in your Content will not cause injury to any third party; and (iv) your Content does not contain viruses or any other programs or technology designed to disrupt or damage any software or hardware.
                    </p>

                </div>

            

                <div class="terms-section intro-section">
                    <h2 class="section-title">8a. Disclaimers and Exclusions</h2>
                    
                    <p class="intro-text text-bold">
                        8.1 Disclaimer of Warranties
                    </p>
                    <p class="intro-text">
                        The CA Graphic DTF Transfers Website and Services are provided on an "as is" and "as available" basis.
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers makes no representations or warranties, express or implied, regarding the operation, availability, reliability, or performance of the Website or Services. Specifically, CA Graphic DTF Transfers does not warrant that:
                    </p>
                    <ul class="bullet-list">
                        <li>The Website or Services will be uninterrupted, timely, secure, or error-free;</li>
                        <li>Any defects or errors will be corrected;</li>
                        <li>The Website, Services, or servers are free of viruses, malware, or other harmful components;</li>
                        <li>The Website or Services will meet your specific requirements or expectations; or</li>
                        <li>The Website or Services will function properly with your hardware, software, internet connection, or device configuration.</li>
                    </ul>
                    <p class="intro-text">
                        Except as expressly stated in these Terms of Service, CA Graphic DTF Transfers disclaims all warranties of any kind, whether express, implied, statutory, or otherwise, including, without limitation, any implied warranties of merchantability, fitness for a particular purpose, title, and non-infringement.
                    </p>

                    <p class="intro-text text-bold">
                        8.2 Limitation of Liability
                    </p>
                    <p class="intro-text">
                        To the fullest extent permitted by law, CA Graphic DTF Transfers shall not be liable for any indirect, incidental, consequential, special, exemplary, or punitive damages arising out of or related to your use of, or inability to use, the Website, Services, or products purchased through the Website.
                    </p>
                    <p class="intro-text">
                        This limitation includes, but is not limited to, damages resulting from:
                    </p>
                    <ul class="bullet-list">
                        <li>Loss of profits or revenue</li>
                        <li>Loss of business opportunities</li>
                        <li>Loss of data or information</li>
                        <li>Loss of goodwill or reputation</li>
                        <li>Service interruptions or delays</li>
                        <li>Errors, omissions, or inaccuracies in Website content</li>
                    </ul>
                    <p class="intro-text">
                        These limitations apply regardless of the legal theory under which the claim arises, including contract, tort, negligence, strict liability, or otherwise, even if CA Graphic DTF Transfers has been advised of the possibility of such damages.
                    </p>

                </div>

                <div class="terms-section intro-section">
                    <h2 class="section-title">8b. Limitation of Liability</h2>
                    
                    <p class="intro-text text-bold">
                        8.3 Limitation of Liability
                    </p>
                    <p class="intro-text">
                        Except for liabilities arising from a party's breach of its representations, warranties, indemnification obligations, or any liability that cannot be limited or excluded under applicable law, the total liability of CA Graphic DTF Transfers shall not exceed the total amount paid by you for the products or services giving rise to the claim.
                    </p>
                    <p class="intro-text">
                        In no event shall CA Graphic DTF Transfers' liability exceed the actual purchase price of the products received or services rendered, as reflected on the applicable order, invoice, or transaction record.
                    </p>
                    <p class="intro-text">
                        The value of any products or services shall be limited to the stated purchase price displayed on the CA Graphic DTF Transfers Website or otherwise quoted by an authorized representative of CA Graphic DTF Transfers.
                    </p>

                </div>



                <div class="terms-section intro-section">
                    <h2 class="section-title">9. Indemnification</h2>
                    
                    <p class="intro-text">
                        You agree to defend, indemnify, and hold harmless CA Graphic DTF Transfers and its owners, officers, directors, managers, employees, agents, representatives, affiliates, successors, and assigns (collectively, the "Protected Parties") from and against any and all claims, demands, actions, liabilities, damages, losses, costs, judgments, settlements, costs, and expenses, including reasonable attorneys' fees and court costs, arising out of or related to:
                    </p>
                    <ul class="bullet-list">
                        <li>Your use of the Website or Services;</li>
                        <li>Your violation of these Terms of Service;</li>
                        <li>Your violation of any applicable law or regulation;</li>
                        <li>Any content, artwork, designs, logos, images, text, or materials you submit, upload, print, distribute, or use through the Services; or</li>
                        <li>Any claim that your submitted content infringes, misappropriates, or otherwise violates the intellectual property rights, privacy rights, publicity rights, or other legal rights of any third party.</li>
                    </ul>
                    <p class="intro-text">
                        If you are required to indemnify CA Graphic DTF Transfers under this section, CA Graphic DTF Transfers reserves the right, at its sole discretion, to assume exclusive control of the defense, settlement, and resolution of any claim, action, or proceeding, at your expense.
                    </p>
                    <p class="intro-text">
                        You agree not to settle, compromise, or otherwise resolve any claim subject to indemnification without the prior written consent of CA Graphic DTF Transfers.
                    </p>

                </div>


                <div class="terms-section intro-section">
                    <h2 class="section-title">10. Termination</h2>
                    
                    <p class="intro-text text-bold">
                        10.1 Termination of Access
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers reserves the right to suspend, restrict, or terminate your access to the Website or Services at any time, with or without notice, if we reasonably believe that you have violated these Terms of Service, applicable laws, or engaged in conduct that may harm CA Graphic DTF Transfers, its customers, or its business operations.
                    </p>

                    <p class="intro-text text-bold">
                        10.2 Survival of Terms
                    </p>
                    <p class="intro-text">
                        Any provisions of these Terms of Service that, by their nature, should survive termination shall remain in effect after termination. Such provisions include, but are not limited to, intellectual property rights, indemnification obligations, limitations of liability, dispute resolution provisions, and any other rights or obligations that are intended to survive termination.
                    </p>

                    <p class="intro-text text-bold">
                        10.3 Effect of Termination
                    </p>
                    <p class="intro-text">
                        Upon termination of your access to the Website or Services, CA Graphic DTF Transfers may deactivate or delete your account, content, files, order history, or other materials associated with your use of the Services, subject to applicable legal and business record keeping requirements.
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers shall not be liable to you or any third party for the suspension, termination, deletion, or removal of any content, data, accounts, or materials resulting from the termination of your access to the Website or Services.
                    </p>

                </div>

            

                <div class="terms-section intro-section">
                    <h2 class="section-title">11. Notice</h2>
                    
                    <p class="intro-text">
                        All notices required or permitted under these Terms of Service must be provided in writing and delivered by one of the following methods:
                    </p>
                    <ul class="bullet-list">
                        <li>United States Mail (U.S. Mail)</li>
                        <li>Overnight Courier Service</li>
                        <li>Electronic Mail (Email)</li>
                    </ul>
                    <p class="intro-text">
                        If you provide notice to CA Graphic DTF Transfers, you must send it to the following address:
                    </p>
                    <p class="intro-text">
                        CA Graphic DTF Transfers<br>
                        1824 S. May St.<br>
                        Chicago, IL 60608<br>
                        United States
                    </p>
                    <p class="intro-text">
                        If CA Graphic DTF Transfers provides notice to you, we will use the contact information associated with your account or otherwise provided by you.
                    </p>
                    <p class="intro-text">
                        Unless otherwise required by applicable law, notices shall be deemed received as follows:
                    </p>
                    <ul class="bullet-list">
                        <li>If sent by U.S. Mail, seven (7) business days after mailing;</li>
                        <li>If sent by overnight courier, on the date delivery is confirmed by the courier service;</li>
                        <li>If sent by email, twenty-four (24) hours after transmission, provided no delivery failure notification or system error message is received.</li>
                    </ul>
                    <p class="intro-text">
                        To the fullest extent permitted by law, you agree that electronic communications, including email messages and electronic notices, satisfy any legal requirement that communications be provided in writing.
                    </p>

                </div>

          

                <div class="terms-section intro-section">
                    <h2 class="section-title">12. Dispute Resolution/Waiver of Jury Trial</h2>
                    
                    <p class="intro-text">
                        Any dispute, claim, or controversy arising out of, relating to, or connected with these Terms of Service or your use of CA Graphic DTF Transfers Website and Services shall be resolved exclusively through confidential and binding arbitration.
                    </p>
                    <p class="intro-text">
                        Such arbitration shall be conducted in Chicago, Illinois, in accordance with the rules of the American Arbitration Association (AAA). The arbitration shall be conducted before a single arbitrator, and the arbitrator shall apply the laws of the State of Illinois without regard to its conflict of law principles.
                    </p>
                    <p class="intro-text">
                        The arbitrator's decision and award shall be final and binding on all parties and may be entered and enforced in a court of competent jurisdiction.
                    </p>
                    <p class="intro-text">
                        To the fullest extent permitted by applicable law, any arbitration conducted under these Terms shall be conducted solely on an individual basis. Class actions, class arbitrations, representative actions, private attorney general actions, and any other consolidated or collective proceeding are expressly prohibited.
                    </p>
                    <p class="intro-text">
                        Except as provided in these Terms or as required by applicable law, no claims arising out of or related to the Website or Services must be brought solely in your individual capacity and not as a plaintiff or class member in any purported class, collective, representative, or consolidated proceeding.
                    </p>
                    <p class="intro-text">
                        Nothing in this section shall prevent either party from seeking temporary, preliminary, or permanent equitable relief, injunctive relief, or other equitable relief in a court of competent jurisdiction when necessary to protect intellectual property rights, confidential information, rights, or other legal interests. No bond or other security shall be required unless otherwise required by applicable law.
                    </p>
                    <p class="intro-text">
                        By agreeing to these Terms of Service, you knowingly and voluntarily waive your right to:
                    </p>
                    <ul class="bullet-list">
                        <li>File a lawsuit in court regarding disputes covered by this arbitration provision;</li>
                        <li>Have disputes decided by a judge or jury;</li>
                        <li>Participate in a class action, class arbitration, or representative proceeding; and</li>
                        <li>Consolidate your claims with the claims of any other person or entity.</li>
                    </ul>

                </div>

           
      

                <div class="terms-section intro-section">
                    <h2 class="section-title">13. Miscellaneous</h2>
                    
                    <p class="intro-text">
                        These Terms of Service shall be binding upon and inure to the benefit of the parties and their respective successors and permitted assigns. These Terms shall be governed by and construed in accordance with the laws of the State of Illinois, without regard to its conflict of law principles.
                    </p>
                    <p class="intro-text">
                        You may not assign, transfer, or delegate any of your rights or obligations under these Terms of Service without the prior written consent of CA Graphic DTF Transfers. Any attempted assignment in violation of this provision shall be null and void.
                    </p>
                    <p class="intro-text">
                        These Terms of Service, together with all policies, agreements, notices, and documents referenced herein, constitute the entire agreement between you and CA Graphic DTF Transfers regarding the Website and Services and supersede all prior understandings, negotiations, representations, and understandings, whether written or oral relating to the same subject matter.
                    </p>
                    <p class="intro-text">
                        No failure or delay by either party in exercising any right, remedy, power, or privilege under these Terms shall operate as a waiver of that right, remedy, power, or privilege. Likewise, any single or partial exercise of any right, remedy, power, or privilege does not preclude the further exercise of that right or the exercise of any other right or privilege.
                    </p>
                    <p class="intro-text">
                        Nothing contained in these Terms of Service shall be construed to create any partnership, joint venture, agency, employment, franchise, or fiduciary relationship between you and CA Graphic DTF Transfers. The parties are and shall remain independent contractors.
                    </p>
                    <p class="intro-text">
                        If any provision of these Terms of Service is determined to be invalid, unlawful, or unenforceable by a court or arbitrator of competent jurisdiction, that provision shall be enforced to the maximum extent permitted by law, and the remaining provisions shall remain in full force and effect.
                    </p>

                </div>

        
           
        </section>
    </div>
</template>

<style scoped>
.terms-of-service-page {
    background: #ffffff;
}

/* Hero Section */
.terms-hero {
    padding: 60px 40px;
    text-align: center;
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
}

.terms-hero h1 {
    font-size: 48px;
    font-weight: 700;
    color: #1a1a1a;
}

/* Main Content */
.terms-content {
    padding: 60px 40px;
    background: #ffffff;
}

.terms-content {
    max-width: 900px;
    margin: 0 auto;
}

.terms-section {
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

.intro-text.text-bold {
    font-weight: bold;
    font-size: 110%;
}

.intro-text strong {
    font-weight: bold;
    color: #1a1a1a;
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

.terms-section p {
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
    .terms-hero {
        padding: 40px 20px;
    }

    .terms-hero h1 {
        font-size: 32px;
    }

    .terms-content {
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
    .terms-hero {
        padding: 30px 16px;
    }

    .terms-hero h1 {
        font-size: 24px;
    }

    .terms-content {
        padding: 30px 16px;
    }

    .section-title {
        font-size: 20px;
    }

    .subsection-title {
        font-size: 15px;
    }

    .terms-section p {
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
