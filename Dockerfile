FROM nginx:alpine

# Xóa web mặc định của nginx
RUN rm -rf /usr/share/nginx/html/*

# Copy toàn bộ web tĩnh
COPY . /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
