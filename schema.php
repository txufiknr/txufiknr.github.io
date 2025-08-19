<?php
ob_start();
echo <<<JSON
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Person",
      "@id": "{URL_WEBSITE}#me",
      "name": "{BIO_FULL_NAME}",
      "alternateName": "txufiknr",
      "legalName": "{BIO_LEGAL_NAME}",
      "jobTitle": "Senior Software Engineer",
      "worksFor": {
        "@id": "https://nurosoft.id/#organization"
      },
      "hasOccupation": [
        {
          "@type": "Occupation",
          "name": "Senior Software Engineer",
          "occupationLocation": {
            "@id": "https://nurosoft.id/#organization"
          },
          "skills": [
            "Flutter", "Dart", "React", "Vue.js", "Angular", "JavaScript", "TypeScript",
            "Node.js", "Next.js", "Express", "Sequelize", "Redis", "PostgreSQL", "Vite",
            "HTML", "CSS", "SCSS", "Kotlin", "Android Studio", "XCode"
          ],
          "startDate": "2019"
        },
        {
          "@type": "Occupation",
          "name": "Programmer",
          "occupationLocation": {
            "@id": "https://www.j-tech.co.id/#organization"
          },
          "skills": [
            "PHP", "JavaScript", "jQuery", "Bootstrap", "HTML", "CSS",
            "CodeIgniter", "MySQL", "MariaDB", "Java", "Android Studio"
          ],
          "startDate": "{JOB_FIRST_YEAR}",
          "endDate": "2019"
        }
      ],
      "url": "{URL_WEBSITE}",
      "image": "{URL_WEBSITE}/{PATH_PHOTO}",
      "description": "Senior Software Engineer specializing in web development and mobile applications.",
      "knowsAbout": [
        "Web Development",
        "Mobile Applications",
        "Frontend Development",
        "Backend Development",
        "Full-stack Engineering",
        "Cross-platform Development",
        "Databases",
        "APIs",
        "UI/UX Design"
      ],
      "sameAs": [
        "{URL_LINKEDIN}",
        "{URL_GITHUB}",
        "{URL_TWITTER}",
        "{URL_PLAYSTORE}"
      ],
      "alumniOf": {
        "@type": "CollegeOrUniversity",
        "name": "Universitas Brawijaya",
        "sameAs": "https://www.ub.ac.id/"
      },
      "homeLocation": {
        "@type": "Place",
        "address": {
          "@type": "PostalAddress",
          "addressCountry": "ID"
        }
      },
      "nationality": {
        "@type": "Country",
        "name": "Indonesia"
      },
      "contactPoint": [
        {
          "@type": "ContactPoint",
          "contactType": "WhatsApp",
          "telephone": "+6285954479380",
          "url": "https://wa.me/6285954479380",
          "availableLanguage": {LANGUAGE_OPTIONS}
        },
        {
          "@type": "ContactPoint",
          "contactType": "Email",
          "email": "flias.test@gmail.com",
          "availableLanguage": {LANGUAGE_OPTIONS}
        }
      ],
      "mainEntityOfPage": {
        "@id": "{URL_WEBSITE}#webpage"
      }
    },
    {
      "@type": "WebSite",
      "@id": "{URL_WEBSITE}#website",
      "name": "{BIO_FULL_NAME} Portfolio",
      "url": "{URL_WEBSITE}",
      "publisher": {
        "@id": "{URL_WEBSITE}#me"
      },
      "hasPart": [
        {
          "@type": "CreativeWork",
          "name": "GitHub Projects",
          "url": "{URL_GITHUB}",
          "creator": {
            "@id": "{URL_WEBSITE}#me"
          }
        },
        {
          "@type": "SoftwareApplication",
          "name": "Android Apps by TARRA Soft",
          "operatingSystem": "Android",
          "applicationCategory": "MobileApplication",
          "url": "{URL_PLAYSTORE}",
          "creator": {
            "@id": "{URL_WEBSITE}#me"
          }
        }
      ]
    },
    {
      "@type": "WebPage",
      "@id": "{URL_WEBSITE}#webpage",
      "name": "{BIO_FULL_NAME} | Portfolio",
      "url": "{URL_WEBSITE}",
      "description": "Portfolio of {BIO_FULL_NAME}, Senior Software Engineer specializing in web development, mobile applications, and open-source projects.",
      "inLanguage": {LANGUAGE_OPTIONS},
      "mainEntity": {
        "@id": "{URL_WEBSITE}#me"
      },
      "publisher": {
        "@id": "{URL_WEBSITE}#me"
      },
      "author": {
        "@id": "{URL_WEBSITE}#me"
      }
    },
    {
      "@type": "Organization",
      "@id": "https://nurosoft.id/#organization",
      "name": "Nurosoft Consulting",
      "legalName": "PT. Nuroho Software Consulting",
      "url": "https://nurosoft.id/",
      "sameAs": [
        "https://id.linkedin.com/company/nurosoft"
      ],
      "foundingDate": "2014",
      "location": {
        "@type": "Place",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "Surabaya",
          "addressCountry": "ID"
        }
      }
    },
    {
      "@type": "Organization",
      "@id": "https://www.j-tech.co.id/#organization",
      "name": "J-Tech Solusindo",
      "legalName": "CV. Jaya Technology Solusindo",
      "url": "https://www.j-tech.co.id/",
      "location": {
        "@type": "Place",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "Malang",
          "addressCountry": "ID"
        }
      }
    }
  ]
}
JSON;
$jsonTemplate = ob_get_clean();
echo strtr($jsonTemplate, [
  '{BIO_FULL_NAME}' => BIO_FULL_NAME,
  '{BIO_LEGAL_NAME}' => BIO_LEGAL_NAME,
  '{JOB_FIRST_YEAR}' => JOB_FIRST_YEAR,
  '{PATH_PHOTO}' => PATH_PHOTO,
  '{URL_WEBSITE}' => URL_WEBSITE,
  '{URL_LINKEDIN}' => URL_LINKEDIN,
  '{URL_GITHUB}' => URL_GITHUB,
  '{URL_TWITTER}' => URL_TWITTER,
  '{URL_PLAYSTORE}' => URL_PLAYSTORE,
  '{LANGUAGE_OPTIONS}' => json_encode(LANGUAGE_OPTIONS),
]);