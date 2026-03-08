<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>
<section class="relative pt-32 pb-24 overflow-hidden min-h-screen">
    <div class="absolute inset-0 z-0">
        <div class="absolute top-20 left-[-10%] w-[500px] h-[500px] bg-primary-100/30 dark:bg-primary-900/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-20 right-[-10%] w-[400px] h-[400px] bg-accent-100/30 dark:bg-accent-900/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-wider text-primary-600 uppercase bg-primary-50 dark:bg-primary-900/30 rounded-full">
                    Get In Touch
                </span>
                <h1 class="text-4xl md:text-6xl font-serif font-bold mb-6">Contact Us</h1>
                <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                    Have questions about our collection or need help with an order? Drop us a message and our team will get back to you within 24 hours.
                </p>
            </div>

            <div class="grid md:grid-cols-5 gap-12 items-start">
                <!-- Contact Info -->
                <div class="md:col-span-2 space-y-8">
                    <div class="glass p-8 rounded-[2rem] border border-white/20 shadow-xl">
                        <h3 class="text-2xl font-serif font-bold mb-6">Contact Details</h3>
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-primary-600/10 rounded-2xl flex items-center justify-center text-primary-600 shrink-0">
                                    <i data-lucide="map-pin" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">Our Store</p>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
                                        392/2, Sarada collage road, autostand, sounth alagapuram /Fair lands, salem Tamil nadu 636016
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-primary-600/10 rounded-2xl flex items-center justify-center text-primary-600 shrink-0">
                                    <i data-lucide="phone" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">Phone</p>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">+91 123 456 7890</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-primary-600/10 rounded-2xl flex items-center justify-center text-primary-600 shrink-0">
                                    <i data-lucide="mail" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">Email</p>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">hello@nssapparels.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="md:col-span-3">
                    <div class="bg-white dark:bg-slate-900 p-8 md:p-10 rounded-[2.5rem] shadow-2xl shadow-primary-500/5 border border-slate-100 dark:border-slate-800">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-2xl border border-green-100 dark:border-green-800 flex items-center gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="mb-8 p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-2xl border border-red-100 dark:border-red-800 flex items-center gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->get('errors')): ?>
                            <div class="mb-8 p-4 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-2xl border border-amber-100 dark:border-amber-800">
                                <ul class="list-disc list-inside text-sm">
                                    <?php foreach (session()->get('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('contact/submit') ?>" method="POST" class="space-y-6">
                            <?= csrf_field() ?>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 px-1">Full Name</label>
                                <input type="text" name="name" value="<?= old('name') ?>" required
                                    class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary-600 transition-all outline-none"
                                    placeholder="John Doe">
                                <?php if (isset(session('errors')['name'])): ?>
                                    <p class="text-red-500 text-xs mt-1 px-1"><?= session('errors')['name'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 px-1">Email Address</label>
                                <input type="email" name="email" value="<?= old('email') ?>" required
                                    class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary-600 transition-all outline-none"
                                    placeholder="john@example.com">
                                <?php if (isset(session('errors')['email'])): ?>
                                    <p class="text-red-500 text-xs mt-1 px-1"><?= session('errors')['email'] ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 px-1">Message / Description</label>
                                <textarea name="description" rows="5" required
                                    class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-primary-600 transition-all outline-none resize-none"
                                    placeholder="Tell us what you're looking for..."><?= old('description') ?></textarea>
                                <?php if (isset(session('errors')['description'])): ?>
                                    <p class="text-red-500 text-xs mt-1 px-1"><?= session('errors')['description'] ?></p>
                                <?php endif; ?>
                            </div>

                            <button type="submit"
                                class="w-full py-5 bg-primary-600 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/40 hover:bg-primary-700 transition-all flex items-center justify-center gap-3 group">
                                Send Message
                                <i data-lucide="send" class="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>