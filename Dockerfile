FROM php:apache

# Copy config files
COPY conf/apache2.conf /etc/apache2/apache2.conf

# Enable apache modules
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start apache
CMD ["apache2-foreground"]
