import { Suspense, lazy } from 'react';
import { Routes, Route } from 'react-router-dom';
import PageSkeleton from './components/PageSkeleton';

const Home = lazy(() => import('./pages/Home'));
const CategoryProducts = lazy(() => import('./pages/CategoryProducts'));
const ProductDetail = lazy(() => import('./pages/ProductDetail'));
const Contact = lazy(() => import('./pages/Contact'));

export default function App() {
  return (
    <Suspense fallback={<PageSkeleton />}>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/products" element={<CategoryProducts />} />
        <Route path="/products/:id" element={<CategoryProducts />} />
        <Route path="/product-detail/:id" element={<ProductDetail />} />
        <Route path="/contact" element={<Contact />} />
      </Routes>
    </Suspense>
  );
}

