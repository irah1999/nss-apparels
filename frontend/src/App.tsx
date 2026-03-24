import { Suspense, lazy } from 'react';
import { Routes, Route } from 'react-router-dom';
import PageSkeleton from './components/PageSkeleton';

import ScrollToTop from './components/ScrollToTop';

const Home = lazy(() => import('./pages/Home'));
const CategoryProducts = lazy(() => import('./pages/CategoryProducts'));
const ProductDetail = lazy(() => import('./pages/ProductDetail'));
const Contact = lazy(() => import('./pages/Contact'));

export default function App() {
  return (
    <Suspense fallback={<PageSkeleton />}>
      <ScrollToTop />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/products" element={<CategoryProducts />} />
        <Route path="/products/:id" element={<CategoryProducts />} />
        <Route path="/product-detail/:id" element={<ProductDetail />} />
        <Route path="/contact" element={<Contact />} />
      </Routes>

      <a 
        href="https://chat.whatsapp.com/LSRiyk1xdAmFNaIrVhybO4" 
        target="_blank" 
        rel="noopener noreferrer" 
        className="fixed bottom-6 right-6 z-50 bg-[#25D366] hover:bg-[#128C7E] text-white p-4 rounded-full shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center animate-bounce hover:animate-none"
        title="Join Community"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
          <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.319 1.592 5.548 0 10.061-4.512 10.063-10.062a10.016 10.016 0 0 0-2.94-7.133 10.016 10.016 0 0 0-7.139-2.943c-5.549 0-10.061 4.513-10.064 10.063 0 2.081.579 3.633 1.62 5.334l-.882 3.226 3.297-.864zm11.455-6.843c-.269-.135-1.594-.786-1.841-.876-.246-.09-.426-.135-.606.135-.18.27-.697.876-.854 1.057-.157.18-.315.202-.584.067-.269-.135-1.138-.419-2.167-1.338-.801-.714-1.341-1.597-1.499-1.867-.157-.27-.017-.417.118-.552.121-.122.269-.315.404-.473.134-.158.18-.27.27-.45.09-.18.045-.337-.023-.472-.068-.135-.606-1.463-.831-2.003-.22-.53-.442-.457-.606-.465l-.518-.006c-.18 0-.472.067-.719.337-.247.27-.943.923-.943 2.25s.966 2.61 1.1 2.79c.135.18 1.901 2.903 4.607 4.07.643.277 1.145.443 1.535.567.645.205 1.233.176 1.7.106.52-.078 1.594-.652 1.819-1.282.225-.63.225-1.17.157-1.282-.068-.113-.247-.203-.516-.338z"/>
        </svg>
      </a>
    </Suspense>
  );
}

