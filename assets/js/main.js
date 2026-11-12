/**
 * Main Front-end JavaScript for Landing Page
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Toggle
  const menuToggle = document.getElementById('menuToggle');
  const navLinks = document.getElementById('navLinks');

  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', () => {
      navLinks.classList.toggle('show');
      const isExpanded = navLinks.classList.contains('show');
      menuToggle.setAttribute('aria-expanded', isExpanded);
    });

    // Close mobile menu on clicking any link
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('show');
      });
    });
  }

  // 2. Portfolio Filtering
  const portfolioFilterBtns = document.querySelectorAll('.portfolio-filter-btn');
  const portfolioCards = document.querySelectorAll('.portfolio-card');

  portfolioFilterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      portfolioFilterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const filterValue = btn.getAttribute('data-filter').toLowerCase();

      portfolioCards.forEach(card => {
        const category = card.getAttribute('data-category').toLowerCase();
        if (filterValue === 'all' || category === filterValue) {
          card.style.display = 'block';
          card.style.opacity = '1';
        } else {
          card.style.display = 'none';
          card.style.opacity = '0';
        }
      });
    });
  });

  // 3. Pricing Category Filtering
  const pricingFilterBtns = document.querySelectorAll('.pricing-filter-btn');
  const pricingCards = document.querySelectorAll('.pricing-card');

  pricingFilterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      pricingFilterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const catValue = btn.getAttribute('data-category').toLowerCase();

      pricingCards.forEach(card => {
        const cardCat = card.getAttribute('data-category').toLowerCase();
        if (catValue === 'all' || cardCat === catValue) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // 4. Expanded Feature Directory Category Tabs
  const dirTabBtns = document.querySelectorAll('.dir-tab-btn');
  const dirCards = document.querySelectorAll('.directory-card');

  dirTabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      dirTabBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const code = btn.getAttribute('data-category-code');

      dirCards.forEach(card => {
        const cardCode = card.getAttribute('data-category-code');
        if (code === 'all' || cardCode === code) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // 5. FAQ Accordion
  const faqItems = document.querySelectorAll('.faq-item');

  faqItems.forEach(item => {
    const questionBtn = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');

    if (questionBtn && answer) {
      questionBtn.addEventListener('click', () => {
        const isActive = item.classList.contains('active');

        // Close all other items
        faqItems.forEach(otherItem => {
          if (otherItem !== item) {
            otherItem.classList.remove('active');
            const otherAnswer = otherItem.querySelector('.faq-answer');
            if (otherAnswer) otherAnswer.style.maxHeight = null;
          }
        });

        // Toggle current item
        if (isActive) {
          item.classList.remove('active');
          answer.style.maxHeight = null;
        } else {
          item.classList.add('active');
          answer.style.maxHeight = answer.scrollHeight + 'px';
        }
      });
    }
  });

  // Open the first FAQ item by default
  if (faqItems.length > 0) {
    const firstItem = faqItems[0];
    const firstAnswer = firstItem.querySelector('.faq-answer');
    firstItem.classList.add('active');
    if (firstAnswer) firstAnswer.style.maxHeight = firstAnswer.scrollHeight + 'px';
  }
});
